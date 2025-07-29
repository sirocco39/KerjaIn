<?php

return [
    // Auth & Registration
    'login_berhasil' => 'Login successful! Welcome, :nama!',
    'logout_berhasil' => 'You have been successfully logged out.',
    'email_atau_kata_sandi_salah' => 'Incorrect email or password.',
    'akun_diblokir' => 'Your account has been blocked. Please contact the administrator.',
    'admin_selamat_datang' => 'Welcome, Admin :nama! Please choose your destination.',
    'otp_kadaluwarsa' => 'OTP has expired or has not been requested.',
    'otp_tidak_valid' => 'Invalid OTP.',
    'daftar_berhasil' => 'Registration successful! Welcome, :nama!',
    'pendaftaran_berhasil_disubmit' => 'Your registration has been submitted for verification!',
    'lengkapi_langkah_sebelumnya' => 'Please complete the previous steps first.',
    
    // Jobs & Transactions
    'pekerjaan_berhasil_dibuat' => 'Job successfully created!',
    'pekerjaan_berhasil_diperbarui' => 'Job successfully updated!',
    'pekerjaan_dibatalkan_refund' => 'Job successfully cancelled and funds of :amount have been returned.',
    'pekerjaan_berhasil_diterima' => 'Job successfully accepted!',
    'pekerjaan_dibatalkan_dana_kembali' => 'The job has been cancelled and funds of :amount have been returned.',
    'pekerjaan_berhasil_dibatalkan' => 'The job has been successfully cancelled.',
    'laporan_berhasil_dikirim' => 'Report sent successfully and will be reviewed shortly.',

    // Finance & Top Up
    'gagal_membuat_invoice' => 'Failed to create payment invoice: :error',

    // Navigation & Roles
    'beralih_ke_requester' => 'You have successfully switched to the Job Requester role, :nama!',
    'beralih_ke_taker' => 'You have successfully switched to the Job Taker role, :nama!',
    'bahasa_diubah' => 'Bahasa berhasil diubah ke :locale.',

    // Errors & Permissions
    'halaman_tidak_ditemukan' => 'Page not found.',
    'bahasa_tidak_didukung' => 'Language not supported.',
    'anda_tidak_berwenang' => 'You are not authorized to perform this action.',
    'anda_tidak_berwenang_melihat' => 'You are not authorized to view this page.',
    'harus_jadi_pekerja' => 'You must be a verified worker to access this page.',
    'sudah_jadi_pekerja' => 'You are already registered as a worker.',
    'verifikasi_diproses' => 'Your verification request is being processed or has already been approved.',
    'terjadi_kesalahan' => 'An error occurred while processing your request.',
    'saldo_tidak_cukup_untuk_pekerjaan' => 'Your balance is not sufficient to create this job.',
    'pekerjaan_sudah_dimulai' => 'This job has already started or the start time has passed and cannot be changed.',
    'pekerjaan_sedang_berjalan' => 'Jobs that are in progress or completed cannot be cancelled.',
];