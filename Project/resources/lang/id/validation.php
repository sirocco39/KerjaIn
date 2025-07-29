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
        'workTitleLabel' => [
            'required' => 'Judul pekerjaan tidak boleh kosong.',
        ],
        'datetime' => [
            'after_start_time' => 'Waktu selesai harus setelah waktu mulai.'
        ],
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
    ],

];