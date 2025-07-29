<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\VerificationRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkerRegistrationController extends Controller
{
    use ValidatesRequests;

    /**
     * Handles the image upload and OCR extraction for KTP.
     * GD-based image preprocessing has been removed.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ocrKtpAjax(Request $request)
    {
        Log::debug('ocrKtpAjax method called.');

        $request->validate([
            'image' => 'required|image|max:5120', // Max 5MB
        ]);

        try {
            $uploadedFile = $request->file('image');
            $originalPath = $uploadedFile->store('ktp_temp_original'); // Store original
            $originalImagePath = storage_path('app/' . $originalPath);

            // Extract text using Tesseract OCR directly from the original image.
            $rawText = (new TesseractOCR($originalImagePath))
                ->lang('ind') // Use 'ind' for Indonesian language
                ->run();

            Log::debug("Tesseract Raw Output: " . $rawText);

            // Parse the raw OCR text into structured data
            $parsedData = $this->parseKtpData($rawText);

            // Store parsed_data in session
            Session::put('worker_registration.ocr_data', $parsedData);
            Log::debug('Parsed OCR Data stored in session: ' . json_encode($parsedData));

            // Delete temporary original image after processing
            Storage::delete($originalPath);

            return response()->json([
                'success' => true,
                'message' => 'OCR processing successful.',
                'raw_text' => $rawText,
                'parsed_data' => $parsedData,
            ]);
        } catch (\Throwable $e) {
            Log::error("KTP OCR Extraction Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to process KTP image. Please try again or ensure the image is clear.',
                'message' => 'Internal server error. Check server logs for details. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parses the OCR text to extract structured KTP data based on line-by-line analysis.
     * Extracts NIK, full_name, birthdate, gender, and a complete address.
     *
     * @param string $ocrText The raw text output from OCR.
     * @return array An associative array containing parsed KTP data.
     */
    private function parseKtpData(string $ocrText): array
    {
        $data = [
            'nik' => null,
            'full_name' => null,
            'birthdate' => null,
            'gender' => null,
            'address' => null, // This will be the combined address
            'raw_ocr_output' => $ocrText,
        ];

        // Normalize line endings and clean up multiple spaces, and then split into lines
        // IMPORTANT: Perform general cleaning on the *entire* raw text before line splitting
        $normalizedText = preg_replace('/\s+/', ' ', $ocrText);
        $normalizedText = preg_replace('/(?<!\w)\s*\:\s*/', ': ', $normalizedText); // Normalize colon spacing
        $lowerCleanedText = strtolower($normalizedText); // This should be used for all general regex checks

        // Split original OCR text into lines for line-specific processing
        $lines = array_map('trim', explode("\n", $ocrText));

        // A temporary array to store address parts as they are found
        $tempAddressParts = [
            'street' => null,
            'rt_rw' => null,
            'kel_desa' => null,
            'kecamatan' => null,
            'city' => null,
            'province' => null,
        ];

        foreach ($lines as $line) {
            $lowerLine = strtolower($line); // Use lowerLine for line-specific checks

            // NIK
            // Prioritize specific pattern like 'q529k' if present, otherwise generic 'nik' or digits
            if (!$data['nik'] && preg_match('/(?:nik|q[0-9]{1}29k)\s*[:\-\s]*(\d{16})/i', $lowerLine, $matches)) {
                $data['nik'] = $matches[1];
                Log::debug("Parsed NIK: " . $data['nik']);
                continue; // Move to next line once NIK is found
            }

            // Full Name
            if (!$data['full_name'] && preg_match('/nama(?:\s*asli\s*anda)?\s*[:\-\s]*([a-z\s\.]+)/i', $lowerLine, $matches)) {
                $data['full_name'] = ucwords(trim($matches[1], '.:- '));
                Log::debug("Parsed Full Name: " . $data['full_name']);
                continue;
            }

            // Birthplace and Date of Birth
            if (!$data['birthdate'] && preg_match('/(?:tempat\/tgl lahir|kota lahir|tgl lahir)\s*[:\-\s]*([a-z\s\.,]+?)\s*[,-\/]?\s*(\d{2}[-\/]\d{2}[-\/]\d{4})/i', $lowerLine, $matches)) {
                // Birthplace raw part is $matches[1] but not stored as separate field
                $dateString = preg_replace('/[-\/]/', '-', trim($matches[2])); // Normalize date separator
                try {
                    $parsedDate = Carbon::createFromFormat('d-m-Y', $dateString);
                    if ($parsedDate) {
                        $data['birthdate'] = $parsedDate->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    Log::warning("Could not parse birthdate '" . $dateString . "': " . $e->getMessage());
                }
                Log::debug("Parsed Birthdate: " . $data['birthdate']);
                continue;
            }

            // Gender
            if (!$data['gender'] && preg_match('/jenis\s*kelamin\s*[:\-\s]*(laki-laki|perempuan)/i', $lowerLine, $matches)) {
                $data['gender'] = ucwords($matches[1]);
                Log::debug("Parsed Gender: " . $data['gender']);
                continue;
            }
            // Fallback for gender if not explicitly labeled but keywords are present
            if (!$data['gender'] && (str_contains($lowerLine, 'laki-laki') || str_contains($lowerLine, 'perempuan'))) {
                if (str_contains($lowerLine, 'laki-laki')) {
                    $data['gender'] = 'Laki-Laki';
                } elseif (str_contains($lowerLine, 'perempuan')) {
                    $data['gender'] = 'Perempuan';
                }
                Log::debug("Parsed Gender (keyword fallback): " . $data['gender']);
                continue;
            }

            // Address components - capture them into tempAddressParts
            // Prioritize 'Alamat' label for street
            if (preg_match('/alamat\s*[:\-\s]*(.*?)(?:\s+(?:rt\/rw|kel\/desa|kecamatan|kota|provinsi|agama|status|pekerjaan|$))/i', $lowerLine, $matches)) {
                $tempAddressParts['street'] = ucwords(trim($matches[1], '.:- '));
                Log::debug("Parsed Address Street (from label): " . $tempAddressParts['street']);
                continue;
            }

            // RT/RW
            if (preg_match('/rt\s*\/?\s*rw\s*[:\-\s]*(\d{1,3}\s*\/?\s*\d{1,3})/i', $lowerLine, $matches)) {
                $tempAddressParts['rt_rw'] = trim($matches[1]);
                Log::debug("Parsed RT/RW: " . $tempAddressParts['rt_rw']);
                continue;
            }

            // Kelurahan/Desa
            if (preg_match('/kel(?:\/|\\\)desa\s*[:\-\s]*([a-z\s\.]+)/i', $lowerLine, $matches)) {
                $tempAddressParts['kel_desa'] = ucwords(trim($matches[1], '.:- '));
                Log::debug("Parsed Kel/Desa: " . $tempAddressParts['kel_desa']);
                continue;
            } elseif (preg_match('/\b(kelurahan|desa)\s+([a-z\s\.]+)/i', $lowerLine, $matches)) { // Fallback
                 $tempAddressParts['kel_desa'] = ucwords(trim($matches[2], '.:- '));
                 Log::debug("Parsed Kel/Desa (fallback): " . $tempAddressParts['kel_desa']);
                 continue;
            }

            // Kecamatan
            if (preg_match('/kecamatan\s*[:\-\s]*([a-z\s\.]+)/i', $lowerLine, $matches)) {
                $tempAddressParts['kecamatan'] = ucwords(trim($matches[1], '.:- '));
                Log::debug("Parsed Kecamatan: " . $tempAddressParts['kecamatan']);
                continue;
            }
        }

        // --- Post-loop processing for address components ---
        // Handle city/province from general text if not found explicitly labeled
        if (preg_match('/(?:ke|kota|kabupaten)\s+([a-z\s]+?)(?:\s+provinsi)?(?=\s*(?:nik|nama|tempat\/tgl lahir|alamat|$))/i', $lowerCleanedText, $matches)) {
            $potentialCityProvince = ucwords(trim($matches[1]));
            // Check if it looks like "City Province" or just "City" / "Province"
            if (strpos($potentialCityProvince, ' ') !== false) {
                $words = explode(' ', $potentialCityProvince);
                $lastWord = array_pop($words);
                // Heuristic: if last word is a common city or 'jakarta', assume it's part of city name or city itself
                if (preg_match('/\b(jakarta|bandung|surabaya|yogyakarta|medan|malang|semarang|palembang|batam|batubara)\b/i', $lastWord)) {
                    $tempAddressParts['city'] = ucwords($lastWord);
                    $tempAddressParts['province'] = ucwords(implode(' ', $words));
                    Log::debug("Parsed City/Province (heuristic): City=" . $tempAddressParts['city'] . ", Province=" . $tempAddressParts['province']);
                } else {
                    $tempAddressParts['city'] = $potentialCityProvince; // Assume single-part name is city
                    Log::debug("Parsed City (single part heuristic): " . $tempAddressParts['city']);
                }
            } else {
                // If it's a single word, assign to city or province based on context if needed.
                // For now, default to city.
                $tempAddressParts['city'] = $potentialCityProvince;
                Log::debug("Parsed City (single word): " . $tempAddressParts['city']);
            }
        }

        // Combine all address parts into the final 'address' field
        $combinedAddressParts = [];

        // Order them logically from specific to general
        if ($tempAddressParts['street']) {
            $combinedAddressParts[] = $tempAddressParts['street'];
        }
        if ($tempAddressParts['rt_rw']) {
            $combinedAddressParts[] = 'RT/RW ' . $tempAddressParts['rt_rw'];
        }
        if ($tempAddressParts['kel_desa']) {
            $combinedAddressParts[] = 'Kel/Desa ' . $tempAddressParts['kel_desa'];
        }
        if ($tempAddressParts['kecamatan']) {
            $combinedAddressParts[] = 'Kecamatan ' . $tempAddressParts['kecamatan'];
        }
        if ($tempAddressParts['city']) {
            $combinedAddressParts[] = 'Kota ' . $tempAddressParts['city'];
        }
        if ($tempAddressParts['province']) {
            $combinedAddressParts[] = 'Provinsi ' . $tempAddressParts['province'];
        }

        $data['address'] = implode(', ', array_filter($combinedAddressParts));

        // If 'address' is still empty, and 'KOTA ANDA' is in the raw text, assign it.
        if (empty($data['address']) && preg_match('/\b(kota\s+anda)\b/i', $lowerCleanedText)) {
             $data['address'] = 'Kota Anda';
             Log::debug("Fallback Address to 'Kota Anda': " . $data['address']);
        }


        // FINAL STEP: Convert all remaining null values to empty strings and perform final cleanup.
        $data = array_map(function ($value) {
            if (is_string($value)) {
                $value = preg_replace('/\s*[-_—]+\s*$/u', '', $value); // Remove trailing dashes/underscores
                $value = preg_replace('/\s+\W*\s*$/u', '', $value); // Remove trailing non-alphanumeric junk
                return trim($value, '.:- /\\'); // Trim common punctuation from ends
            }
            return $value === null ? "" : $value; // Convert null to empty string
        }, $data);

        return $data;
    }

    public function createStep1()
    {
        $verificationRequest = VerificationRequest::where('user_id', Auth::id())->first();
        if ($verificationRequest) {
            return redirect()->route('worker.register.pending')->with('custom_success_alert', 'Permintaan verifikasi Anda sedang diproses atau sudah disetujui.');
            // If there's a pending or approved verification request, redirect to the pending page
            return redirect()->route('worker.register.pending')->with('custom_success_alert', __('alerts.verifikasi_diproses'));
        }
        $data = Session::get('worker_registration.step1', []);
        $ocrData = Session::get('worker_registration.ocr_data', []);
        return view('join-worker.join', compact('data', 'ocrData'));
    }

    public function store1(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\-.\']+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\-.\']+$/',
            'nik' => [
                'required',
                'string',
                'digits:16',
                Rule::unique('verification_requests', 'nik')->where(function ($query) {
                    return $query->whereIn('status', ['pending', 'approved']);
                }),
            ],
            'birthdate' => 'required|date|before_or_equal:' . now()->subYears(17)->format('Y-m-d'),
            'gender' => 'required|in:Male,Female',
            'address' => 'required|string|max:255',
            'phone_number' => [
                'required',
                'string',
                'regex:/^(08)[0-9]{9,10}$/',
                Rule::unique('verification_requests', 'phone_number')->where(function ($query) {
                    return $query->whereIn('status', ['pending', 'approved']);
                }),
            ],
        ], [
            'first_name.required' => 'Nama depan tidak boleh kosong.',
            'first_name.string' => 'Nama depan harus berupa teks.',
            'first_name.regex' => 'Nama depan hanya boleh mengandung huruf',
            'last_name.required' => 'Nama belakang tidak boleh kosong.',
            'last_name.string' => 'Nama belakang harus berupa teks.',
            'last_name.regex' => 'Nama belakang hanya boleh mengandung huruf',
            'address.required' => 'Alamat tidak boleh kosong.',
            'gender.required' => 'Jenis kelamin tidak boleh kosong.',
            'nik.required' => 'NIK tidak boleh kosong.',
            'nik.digits' => 'NIK harus terdiri from 16 digit.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'phone_number.required' => 'Nomor telepon tidak boleh kosong.',
            'birthdate.before_or_equal' => 'Usia harus minimal 17 tahun.',
            'phone_number.regex' => 'Masukkan nomor telepon yang valid dengan format 08XXXXXXXXXX.',
            'phone_number.unique' => 'Nomor telepon sudah terdaftar.',
        ]);


        Session::put('worker_registration.step1', [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'nik' => $request->nik,
            'birthdate' => $request->birthdate,
            'gender' => $request->gender,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
        ]);

        return redirect()->route('worker.register.step2');
    }

    public function createStep2()
    {
        if (!Session::has('worker_registration.step1')) {
            return redirect()->route('worker.register.step1')->with('custom_error_alert', __('alerts.lengkapi_langkah_sebelumnya'));
        }
        return view('join-worker.join2');
    }

    public function store2(Request $request)
    {
        if (!Session::has('worker_registration.step1')) {
            return redirect()->route('worker.register.step1')->with('custom_error_alert', __('alerts.lengkapi_langkah_sebelumnya'));
        }

        $request->validate([
            'agree_terms' => 'accepted',
            'agree_data_usage' => 'accepted',
        ], [
            'agree_terms.accepted' => 'Anda harus menyetujui Syarat dan Ketentuan.',
            'agree_data_usage.accepted' => 'Anda harus menyetujui penggunaan data untuk verifikasi dan keamanan.',
        ]);

        Session::put('worker_registration.step2', ['completed' => true]);

        return redirect()->route('worker.register.step3');
    }

    public function createStep3()
    {
        if (!Session::has('worker_registration.step1') || !Session::has('worker_registration.step2')) {
            return redirect()->route('worker.register.step1')->with('custom_error_alert', __('alerts.lengkapi_langkah_sebelumnya'));
        }

        $step1Data = Session::get('worker_registration.step1', []);
        $ocrData = Session::get('worker_registration.ocr_data', []);

        $allData = array_merge(
            $step1Data,
            $ocrData
        );

        return view('join-worker.join3', compact('allData', 'ocrData'));
    }

    private function storeFile(Request $request, string $inputName, string $basePath, int $userId): ?string
    {
        if ($request->hasFile($inputName)) {
            $file = $request->file($inputName);
            $filename = $inputName . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = "{$basePath}/user_{$userId}";
            return $file->storeAs($path, $filename, 'public');
        }
        return null;
    }


    public function finalizeRegistration(Request $request)
    {
        if (!Session::has('worker_registration.step1') || !Session::has('worker_registration.step2')) {
            return redirect()->route('worker.register.step1')->with('custom_error_alert', __('alerts.lengkapi_langkah_sebelumnya'));
        }

        $step1Data = Session::get('worker_registration.step1');
        $ocrData = Session::get('worker_registration.ocr_data', []);

        $this->validate($request, [
            'photo_url' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'id_card_url' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'selfie_with_id_card_url' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:10',
        ], [
            'photo_url.required' => 'Foto Diri wajib diunggah.',
            'photo_url.image' => 'File harus berupa gambar.',
            'photo_url.mimes' => 'Format file Foto Diri harus JPEG, PNG, atau JPG.',
            'photo_url.max' => 'Ukuran Foto Diri tidak boleh lebih dari 5MB.',

            'id_card_url.required' => 'Foto KTP wajib diunggah.',
            'id_card_url.image' => 'File harus berupa gambar.',
            'id_card_url.mimes' => 'Format file Foto KTP harus JPEG, PNG, atau JPG.',
            'id_card_url.max' => 'Ukuran Foto KTP tidak boleh lebih dari 5MB.',

            'selfie_with_id_card_url.required' => 'Foto Diri dan KTP wajib diunggah.',
            'selfie_with_id_card_url.image' => 'File harus berupa gambar.',
            'selfie_with_id_card_url.mimes' => 'Format file Foto Diri dan KTP harus JPEG, PNG, atau JPG.',
            'selfie_with_id_card_url.max' => 'Ukuran Foto Diri dan KTP tidak boleh lebih dari 5MB.',

            'account_name.required' => 'Nama Pemilik Rekening wajib diisi.',
            'account_name.string' => 'Nama Pemilik Rekening harus berupa teks.',
            'account_name.max' => 'Nama Pemilik Rekening tidak boleh lebih dari 255 karakter.',

            'account_number.required' => 'Nomor Rekening wajib diisi.',
            'account_number.string' => 'Nomor Rekening harus berupa teks.',
            'account_number.max' => 'Nomor Rekening tidak boleh lebih dari 10 karakter.',
        ]);

        $userId = Auth::id();
        $storagePath = 'public/worker_verification_documents';
        $selfiePhotoPath = $this->storeFile($request, 'photo_url', $storagePath, $userId);
        $idCardPhotoPath = $this->storeFile($request, 'id_card_url', $storagePath, $userId);
        $selfieWithIdCardPhotoPath = $this->storeFile($request, 'selfie_with_id_card_url', $storagePath, $userId);

        try {
            DB::transaction(function () use (
                $userId,
                $step1Data,
                $ocrData,
                $selfiePhotoPath,
                $idCardPhotoPath,
                $selfieWithIdCardPhotoPath,
                $request
            ) {
                $verificationRequest = VerificationRequest::where('user_id', Auth::id())->first();

                // Prepare verification data from user input
                $verificationData = [
                    'user_id' => $userId,
                    'status' => 'pending',
                    'first_name' => $step1Data['first_name'],
                    'last_name' => $step1Data['last_name'],
                    'nik' => $step1Data['nik'],
                    'birthdate' => $step1Data['birthdate'],
                    'gender' => $step1Data['gender'],
                    'address' => $step1Data['address'],
                    'phone_number' => $step1Data['phone_number'],
                    'photo_url' => Storage::url($selfiePhotoPath),
                    'id_card_url' => Storage::url($idCardPhotoPath),
                    'selfie_with_id_card_url' => Storage::url($selfieWithIdCardPhotoPath),
                    'account_name' => $request->input('account_name'),
                    'account_number' => $request->input('account_number'),
                ];

                // Add OCR data
                $verificationData['ocr_nik'] = $ocrData['nik'] ?? null;
                $verificationData['ocr_full_name'] = $ocrData['full_name'] ?? null;
                $verificationData['ocr_birthdate'] = $ocrData['birthdate'] ?? null;
                $verificationData['ocr_gender'] = $ocrData['gender'] ?? null;
                $verificationData['ocr_address'] = $ocrData['address'] ?? null;
                $verificationData['ocr_raw_output'] = $ocrData['raw_ocr_output'] ?? null;


                if ($verificationRequest) {
                    $verificationRequest->update($verificationData);
                } else {
                    $verificationRequest = VerificationRequest::create($verificationData);
                }
            });

            Session::forget('worker_registration');
            return redirect()->route('worker.register.success')->with('custom_blue_alert', 'Pendaftaran Anda berhasil disubmit untuk verifikasi!');
        } catch (\Exception $e) {
            Log::error("Error finalizing worker registration for user " . Auth::id() . ": " . $e->getMessage(), ['exception' => $e]);
            return back()->with('custom_error_alert', 'Terjadi kesalahan saat finalisasi pendaftaran: ' . $e->getMessage());
        }
        Session::forget('worker_registration');
        return redirect()->route('worker.register.success')->with('custom_blue_alert', __('alerts.pendaftaran_berhasil_disubmit'));
    }

    public function showSuccessPage()
    {
        return view('join-worker.success');
    }

    public function showPendingPage()
    {
        return view('join-worker.pending');
    }
}
