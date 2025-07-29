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
use Illuminate\Support\Facades\Log; // Import Log facade

class WorkerRegistrationController extends Controller
{
    use ValidatesRequests;

    public function ocrKtpAjax(Request $request)
    {
        $request->validate([
            'ktp_image' => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        try {
            $path = $request->file('ktp_image')->store('ktp_images', 'public');
            $fullPath = storage_path('app/public/' . $path);

            $ocrText = (new TesseractOCR($fullPath)) // Use imported TesseractOCR
                ->lang('ind')
                ->psm(6)
                ->run();

            preg_match('/\b\d{16}\b/', $ocrText, $matches);
            $nik = $matches[0] ?? null;

            // Delete the temporary uploaded image after OCR
            Storage::disk('public')->delete($path);

            if ($nik) {
                // Store the scanned NIK in the session
                Session::put('worker_registration.scanned_nik', $nik);
                Log::info("OCR KTP Success: NIK {$nik} stored in session for user " . Auth::id());
                return response()->json(['success' => true, 'message' => 'NIK ' . $nik . ' dari KTP berhasil dipindai dan disimpan ke sesi.']);
            } else {
                // Clear scanned_nik from session if not found
                Session::forget('worker_registration.scanned_nik');
                Log::warning("OCR KTP Failed: NIK not found in image for user " . Auth::id());
                return response()->json(['success' => false, 'message' => 'NIK tidak ditemukan pada gambar. Silakan unggah ulang.']);
            }
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error("OCR KTP Error for user " . Auth::id() . ": " . $e->getMessage());
            // Clear scanned_nik from session on error
            Session::forget('worker_registration.scanned_nik');
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memproses gambar KTP.']);
        }
    }


    // Method untuk menampilkan form langkah 1 (Data Pribadi)
    public function createStep1()
    {
        $verificationRequest = VerificationRequest::where('user_id', Auth::id())->first();
        if ($verificationRequest) {
            // If there's a pending or approved verification request, redirect to the pending page
            return redirect()->route('worker.register.pending')->with('custom_success_alert', __('alerts.verifikasi_diproses'));
        }
        // Ambil data dari session jika ada, untuk mengisi ulang form
        $data = Session::get('worker_registration.step1', []);
        return view('join-worker.join', compact('data'));
    }

    public function store1(Request $request)
    {
        // Validate input data
        $this->validate($request, [
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\-.\']+$/', // Added regex
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\-.\']+$/',  // Added regex
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
            'first_name.string' => 'Nama depan harus berupa teks.', // Better message for string type
            'first_name.regex' => 'Nama depan hanya boleh mengandung huruf', // New message for regex
            'last_name.required' => 'Nama belakang tidak boleh kosong.',
            'last_name.string' => 'Nama belakang harus berupa teks.', // Better message for string type
            'last_name.regex' => 'Nama belakang hanya boleh mengandung huruf', // New message for regex
            'address.required' => 'Alamat tidak boleh kosong.',
            'gender.required' => 'Jenis kelamin tidak boleh kosong.',
            'nik.required' => 'NIK tidak boleh kosong.',
            'nik.digits' => 'NIK harus terdiri dari 16 digit.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'phone_number.required' => 'Nomor telepon tidak boleh kosong.',
            'birthdate.before_or_equal' => 'Usia harus minimal 17 tahun.',
            'phone_number.regex' => 'Masukkan nomor telepon yang valid dengan format 08XXXXXXXXXX.',
            'phone_number.unique' => 'Nomor telepon sudah terdaftar.',
        ]);


        // Simpan data langkah 1 ke sesi
        Session::put('worker_registration.step1', [
            // user_id dan status tidak disimpan di sini karena akan di-handle di finalizeRegistration
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'nik' => $request->nik,
            'birthdate' => $request->birthdate,
            'gender' => $request->gender, // Simpan format mentah 'Laki-laki'/'Perempuan'
            'address' => $request->address,
            'phone_number' => $request->phone_number,
        ]);

        return redirect()->route('worker.register.step2');
    }

    // Method untuk menampilkan form langkah 2
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

        // Validasi untuk agree_terms tetap dipertahankan karena ada 'required' di HTML
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
        $step3Data = Session::get('worker_registration.step3', []);

        $allData = array_merge(
            $step1Data,
            $step3Data
        );

        return view('join-worker.join3', compact('allData', 'step3Data'));
    }

    private function storeFile(Request $request, string $inputName, string $basePath, int $userId): ?string
    {
        if ($request->hasFile($inputName)) {
            $file = $request->file($inputName);
            $filename = $inputName . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = "{$basePath}/user_{$userId}";
            return $file->storeAs($path, $filename);
        }

        return null;
    }


    public function finalizeRegistration(Request $request)
    {
        // Pastikan langkah 1 dan 2 sudah selesai sebelum melanjutkan
        if (!Session::has('worker_registration.step1') || !Session::has('worker_registration.step2')) {
            return redirect()->route('worker.register.step1')->with('custom_error_alert', __('alerts.lengkapi_langkah_sebelumnya'));
        }

        // Ambil data langkah 1 dari sesi
        $step1Data = Session::get('worker_registration.step1');
        // Retrieve scanned_nik from session
        $scannedNik = Session::get('worker_registration.scanned_nik');


        // Validasi input untuk unggahan file dan detail pembayaran
        $this->validate($request, [
            'photo_url' => 'required|image|mimes:jpeg,png,jpg|max:122880',
            'id_card_url' => 'required|image|mimes:jpeg,png,jpg|max:122880',
            'selfie_with_id_card_url' => 'required|image|mimes:jpeg,png,jpg|max:122880',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:10',
        ], [
            'photo_url.required' => 'Foto Diri wajib diunggah.',
            'photo_url.image' => 'File harus berupa gambar.',
            'photo_url.mimes' => 'Format file Foto Diri harus JPEG, PNG, atau JPG.',
            'photo_url.max' => 'Ukuran Foto Diri tidak boleh lebih dari 120MB.',

            'id_card_url.required' => 'Foto KTP wajib diunggah.',
            'id_card_url.image' => 'File harus berupa gambar.',
            'id_card_url.mimes' => 'Format file Foto KTP harus JPEG, PNG, atau JPG.',
            'id_card_url.max' => 'Ukuran Foto KTP tidak boleh lebih dari 120MB.',

            'selfie_with_id_card_url.required' => 'Foto Diri dan KTP wajib diunggah.',
            'selfie_with_id_card_url.image' => 'File harus berupa gambar.',
            'selfie_with_id_card_url.mimes' => 'Format file Foto Diri dan KTP harus JPEG, PNG, atau JPG.',
            'selfie_with_id_card_url.max' => 'Ukuran Foto Diri dan KTP tidak boleh lebih dari 120MB.',

            'account_name.required' => 'Nama Pemilik Rekening wajib diisi.',
            'account_name.string' => 'Nama Pemilik Rekening harus berupa teks.',
            'account_name.max' => 'Nama Pemilik Rekening tidak boleh lebih dari 255 karakter.',

            'account_number.required' => 'Nomor Rekening wajib diisi.',
            'account_number.string' => 'Nomor Rekening harus berupa teks.',
            'account_number.max' => 'Nomor Rekening tidak boleh lebih dari 10 karakter.',
        ]);

        // Path penyimpanan untuk file yang diunggah
        $userId = Auth::id();
        $storagePath = 'public/worker_verification_documents';
        $selfiePhotoPath = $this->storeFile($request, 'photo_url', $storagePath, $userId);
        $idCardPhotoPath = $this->storeFile($request, 'id_card_url', $storagePath, $userId);
        $selfieWithIdCardPhotoPath = $this->storeFile($request, 'selfie_with_id_card_url', $storagePath, $userId);

        // Siapkan array data untuk model VerificationRequest
        $verificationData = [
            'user_id' => $userId,
            'status' => 'pending',
            'first_name' => $step1Data['first_name'],
            'last_name' => $step1Data['last_name'],
            'nik' => $step1Data['nik'],
            'scanned_nik' => $scannedNik, // Now using scannedNik from session
            'birthdate' => $step1Data['birthdate'],
            'gender' => $step1Data['gender'],
            'address' => $step1Data['address'],
            'phone_number' => $step1Data['phone_number'],
            'photo_url' => $selfiePhotoPath,
            'id_card_url' => $idCardPhotoPath,
            'selfie_with_id_card_url' => $selfieWithIdCardPhotoPath,
            'account_name' => $request->input('account_name'),
            'account_number' => $request->input('account_number'),
        ];

        // Cari record VerificationRequest berdasarkan user_id
        $verificationRequest = VerificationRequest::where('user_id', $userId)->first();

        if ($verificationRequest) {
            $verificationRequest->update($verificationData);
        } else {
            // This case should ideally not happen if OCR is done after step1,
            // but for robustness, we create if not found.
            VerificationRequest::create($verificationData);
        }

        // Bersihkan data sesi pendaftaran setelah finalisasi berhasil
        Session::forget('worker_registration');
        return redirect()->route('worker.register.success')->with('custom_blue_alert', 'Pendaftaran Anda berhasil disubmit untuk verifikasi!');
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
