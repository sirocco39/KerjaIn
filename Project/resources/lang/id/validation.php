<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pesan Error Validasi (Indonesia)
    |--------------------------------------------------------------------------
    */
    'required' => 'Kolom :attribute wajib diisi.',
    'string'   => 'Kolom :attribute harus berupa teks.',
    'numeric'  => 'Kolom :attribute harus berupa angka.',
    'date'     => 'Kolom :attribute bukan tanggal yang valid.',
    'min'      => [
        'numeric' => ':attribute minimal harus :min.',
    ],
    'max'      => [
        'string' => ':attribute maksimal :max karakter.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Atribut Validasi Kustom
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'workTitleLabel'     => 'Judul Pekerjaan',
        'workDetailLabel'    => 'Detail Pekerjaan',
        'workPriceLabel'     => 'Harga',
        'workAddressLabel'   => 'Alamat',
        'workStartDateLabel' => 'Tanggal mulai',
        'workEndDateLabel'   => 'Tanggal selesai',
        'workStartTimeLabel' => 'Waktu mulai',
        'workEndTimeLabel'   => 'Waktu selesai',
        'datetime'           => 'Waktu pekerjaan'
    ],

     /*
    |--------------------------------------------------------------------------
    | Pesan Validasi Kustom
    |--------------------------------------------------------------------------
    */
    'custom' => [
        'datetime' => [
            'after_start_time' => 'Waktu selesai pekerjaan harus setelah waktu mulai.',
        ],
    ],
];