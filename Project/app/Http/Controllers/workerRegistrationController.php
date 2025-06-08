<?php

namespace App\Http\Controllers;

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
        // Ambil data dari session jika ada, untuk mengisi ulang form
        $data = Session::get('worker_registration.step1', []);
        return view('joinWorker.joinn', compact('data'));
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
                'regex:/^(08)[0-9]{9,11}$/',
                Rule::unique('verification_requests', 'phone_number')->where(function ($query) {
                    return $query->whereIn('status', ['pending', 'approved']);
                }),
            ],
        ],[
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
        $this->validate($request, [
            'agree_terms' => 'required|accepted',
        ], [
            'agree_terms.required' => 'Anda harus menyetujui Syarat dan Ketentuan KerjaIn.',
            'agree_terms.accepted' => 'Anda harus menyetujui Syarat dan Ketentuan KerjaIn.',
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
            'account_number' => 'required|string|max:255',
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
            'account_number.max' => 'Nomor Rekening tidak boleh lebih dari 255 karakter.',
        ]);

        // Path penyimpanan untuk file yang diunggah
        $storagePath = 'public/worker_verification_documents';
        $selfiePhotoPath = $request->file('photo_url')->store($storagePath);
        $idCardPhotoPath = $request->file('id_card_url')->store($storagePath);
        $selfieWithIdCardPhotoPath = $request->file('selfie_with_id_card_url')->store($storagePath);

        // Siapkan array data untuk model VerificationRequest
        $verificationData = [
            // 'user_id' => Auth::id(), // user_id pasti ada karena sudah divalidasi Auth::check()
            'user_id' => 1,
            'status' => 'pending',
            'first_name' => $step1Data['first_name'],
            'last_name' => $step1Data['last_name'],
            'nik' => $step1Data['nik'],
            'birthdate' => $step1Data['birthdate'],
            'gender' => $step1Data['gender'] == 'Laki-laki' ? 'Male' : ($step1Data['gender'] == 'Perempuan' ? 'Female' : null),
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
        Session::forget('worker_registration');

        return redirect()->route('worker.register.success')->with('success', 'Pendaftaran Anda berhasil disubmit untuk verifikasi!');
    }

    public function showSuccessPage()
    {
        return view('joinWorker.success');
    }
}