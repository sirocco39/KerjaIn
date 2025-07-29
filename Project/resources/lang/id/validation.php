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
];