<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\VerificationRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class WorkerRegistrationController extends Controller
{
    use ValidatesRequests;

    // Method untuk menampilkan form langkah 1 (Data Pribadi)
    public function createStep1()
    {
        $verificationRequest = VerificationRequest::where('user_id', Auth::id())->first();
        if ($verificationRequest) {
            // Jika ada permintaan verifikasi yang masih pending atau approved, arahkan ke halaman sukses
            return redirect()->route('worker.register.pending');
        }
        // Ambil data dari session jika ada, untuk mengisi ulang form
        $data = Session::get('worker_registration.step1', []);
        return view('joinWorker.join', compact('data'));
    }

    public function store1(Request $request)
    {
        // Validate input data
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
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
            return redirect()->route('worker.register.step1')->with('error', 'Silakan lengkapi Data Pribadi terlebih dahulu.');
        }
        return view('joinWorker.join2');
    }

    public function store2(Request $request)
    {
        if (!Session::has('worker_registration.step1')) {
            return redirect()->route('worker.register.step1')->with('error', 'Silakan lengkapi Data Pribadi terlebih dahulu.');
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
            return redirect()->route('worker.register.step1')->with('error', 'Silakan lengkapi langkah sebelumnya terlebih dahulu.');
        }

        $step1Data = Session::get('worker_registration.step1', []);
        $step3Data = Session::get('worker_registration.step3', []);

        $allData = array_merge(
            $step1Data,
            $step3Data
        );

        return view('joinWorker.join3', compact('allData', 'step3Data'));
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
        // Pastikan pengguna sudah login
        // if (!Auth::check()) {
        //     return redirect()->route('login')->with('error', 'Anda harus login untuk menyelesaikan pendaftaran.'); // Ubah 'login' ke route login Anda
        // }

        // Pastikan langkah 1 dan 2 sudah selesai sebelum melanjutkan
        if (!Session::has('worker_registration.step1') || !Session::has('worker_registration.step2')) {
            return redirect()->route('worker.register.step1')->with('error', 'Silakan lengkapi langkah sebelumnya terlebih dahulu.');
        }

        // Ambil data langkah 1 dari sesi
        $step1Data = Session::get('worker_registration.step1');

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
            // 'user_id' => Auth::id(), // user_id pasti ada karena sudah divalidasi Auth::check()
            'user_id' => $userId,
            'status' => 'pending',
            'first_name' => $step1Data['first_name'],
            'last_name' => $step1Data['last_name'],
            'nik' => $step1Data['nik'],
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

        // dd($request->all(), $verificationData);

        // Cari record VerificationRequest berdasarkan NIK
        $verificationRequest = VerificationRequest::where('nik', $step1Data['nik'])->first();

        if ($verificationRequest) {
            $verificationRequest->update($verificationData);
        } else {
            VerificationRequest::create($verificationData);
        }

        // Bersihkan data sesi pendaftaran setelah finalisasi berhasil
        User::where('id', Auth::id())->update(['is_worker' => 1]);
        Session::forget('worker_registration');
        // update is_worker user jadi 1
        return redirect()->route('worker.register.success')->with('success', 'Pendaftaran Anda berhasil disubmit untuk verifikasi!');
    }

    public function showSuccessPage()
    {
        return view('joinWorker.success');
    }

    public function showPendingPage()
    {
        return view('joinWorker.pending');
    }
}
