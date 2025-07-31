<?php

return [
    'required' => 'Kolom :attribute wajib diisi.',
    'string' => 'Kolom :attribute harus berupa teks.',
    'numeric' => 'Kolom :attribute harus berupa angka.',
    'date' => 'Kolom :attribute harus berupa tanggal yang valid.',
    'min' => [
        'numeric' => ':attribute minimal harus :min.',
    ],
    'custom' => [
        'workStartDateLabel' => [
            'past_utc' => 'Waktu mulai tidak boleh di masa lalu (UTC).',
        ],
        'datetime' => [
            'after_start_time' => 'Waktu selesai harus setelah waktu mulai.',
        ],
        // Validasi unggahan file dari WorkerRegistrationController
        'photo_url' => [
            'required' => 'Foto Diri wajib diunggah.',
            'image' => 'File harus berupa gambar.',
            'mimes' => 'Format file Foto Diri harus JPEG, PNG, atau JPG.',
            'max' => 'Ukuran Foto Diri tidak boleh lebih dari :max MB.',
        ],
        'id_card_url' => [
            'required' => 'Foto KTP wajib diunggah.',
            'image' => 'File harus berupa gambar.',
            'mimes' => 'Format file Foto KTP harus JPEG, PNG, atau JPG.',
            'max' => 'Ukuran Foto KTP tidak boleh lebih dari :max MB.',
        ],
        'selfie_with_id_card_url' => [
            'required' => 'Foto Diri dan KTP wajib diunggah.',
            'image' => 'File harus berupa gambar.',
            'mimes' => 'Format file Foto Diri dan KTP harus JPEG, PNG, atau JPG.',
            'max' => 'Ukuran Foto Diri dan KTP tidak boleh lebih dari :max MB.',
        ],
        'account_name' => [
            'required' => 'Nama Pemilik Rekening wajib diisi.',
            'string' => 'Nama Pemilik Rekening harus berupa teks.',
            'max' => 'Nama Pemilik Rekening tidak boleh lebih dari :max karakter.',
        ],
        'account_number' => [
            'required' => 'Nomor Rekening wajib diisi.',
            'string' => 'Nomor Rekening harus berupa teks.',
            'max' => 'Nomor Rekening tidak boleh lebih dari :max karakter.',
        ],
        'agree_terms' => [
            'accepted' => 'Anda harus menyetujui Syarat dan Ketentuan.',
        ],
        'agree_data_usage' => [
            'accepted' => 'Anda harus menyetujui penggunaan data untuk verifikasi dan keamanan.',
        ],
        'first_name' => [
            'regex' => 'Nama depan hanya boleh mengandung huruf, spasi, tanda hubung, atau titik.',
        ],
        'last_name' => [
            'regex' => 'Nama belakang hanya boleh mengandung huruf, spasi, tanda hubung, atau titik.',
        ],
        'nik' => [
            'digits' => 'NIK harus terdiri dari 16 digit.',
            'unique' => 'NIK sudah terdaftar.',
        ],
        'phone_number' => [
            'regex' => 'Masukkan nomor telepon yang valid dengan format 08XXXXXXXXXX.',
            'unique' => 'Nomor telepon sudah terdaftar.',
        ],
        'birthdate' => [
            'before_or_equal' => 'Usia harus minimal 17 tahun.',
        ],
        'workTitleLabel' => [
            'required' => 'Judul pekerjaan tidak boleh kosong.',
        ],
        // ... validasi kustom lainnya yang mungkin sudah ada ...
    ],
    'attributes' => [
        'workTitleLabel' => 'Judul Pekerjaan',
        'workDetailLabel' => 'Detail Pekerjaan',
        'workPriceLabel' => 'Upah',
        'workAddressLabel' => 'Alamat',
        'workStartDateLabel' => 'Tanggal Mulai',
        'workEndDateLabel' => 'Tanggal Selesai',
        'workStartTimeLabel' => 'Waktu Mulai',
        'workEndTimeLabel' => 'Waktu Selesai',
    ],

    // ... (pesan validasi bawaan Laravel lainnya, biarkan saja)

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Messages for JavaScript
    |--------------------------------------------------------------------------
    | These messages are specifically for frontend JavaScript validation.
    */
    'custom_js' => [
        'firstname_required' => 'Nama depan diperlukan.',
        'lastname_required' => 'Nama belakang diperlukan.',
        'email_required' => 'Email harus diisi.',
        'email_invalid' => 'Silakan masukkan alamat email yang valid.',
        'password_required' => 'Password harus diisi.',
        'password_min_length' => 'Password minimal harus 8 karakter.',
        'password_one_uppercase' => 'Password harus mengandung setidaknya satu huruf kapital.',
        'password_one_lowercase' => 'Password harus mengandung setidaknya satu huruf kecil.',
        'password_one_number' => 'Password harus mengandung setidaknya satu angka.',
        'password_one_symbol' => 'Password harus mengandung setidaknya satu simbol.',
        'confirm_password_required' => 'Konfirmasi kata sandi harus diisi.',
        'confirm_password_match' => 'Kata sandi tidak cocok.',
        'otp_required' => 'OTP harus diisi.',
        'otp_format' => 'OTP harus berupa 6 digit angka.',
        'datetime_start_in_past' => 'Start time cannot be in the past (UTC).',
        'datetime_end_before_start' => 'End time must be after start time.',
    ],

];
