<?php

return [
    // Main Page Text
    'kerjaan_kamu' => 'Your Job',
    'deskripsi' => 'Description',
    'mulai' => 'Start',
    'selesai' => 'End',
    'durasi' => 'Duration',
    'upah' => 'Wage',
    'lokasi' => 'Location',
    'bergabung_sejak' => 'Joined Kerjain since',
    'jam' => 'hours',
    'menit' => 'minutes',

    // Status
    'status' => [
        'ditinjau' => 'Submitted',
        'dibatalkan' => 'Cancelled',
        'diterima' => 'Accepted',
        'dikerjakan' => 'In Progress',
        'selesai' => 'Completed',
    ],

    // Main Action Buttons
    'tombol' => [
        'mulai_kerja' => 'Start Work',
        'batalkan_kerja' => 'Cancel Job',
        'selesai_kerja' => 'Finish Work',
        'sudah_selesai' => 'Completed',
        'berikan_penilaian' => 'Give Rating',
    ],

    // Status Timeline
    'timeline' => [
        'diterima' => 'Accepted',
        'dikerjakan' => 'In Progress',
        'selesai' => 'Completed',
        'ditinjau' => 'Submitted',
    ],

    // Cancel Confirmation Modal
    'modal_batal' => [
        'judul' => 'Cancel Job?',
        'pesan1' => 'Are you sure you want to cancel this job?',
        'pesan2' => 'This action might affect your reputation on the Kerjain platform.',
        'tombol_kembali' => 'Continue Work',
        'tombol_konfirmasi' => 'Yes, Still Cancel',
    ],

    // Completion Proof Modal
    'modal_bukti' => [
        'judul' => 'Confirm Job Completion',
        'label_foto' => 'Upload Job Proof Photo',
        'label_catatan' => 'Note (Optional)',
        'tombol_kembali' => 'Back',
        'tombol_selesaikan' => 'Complete Job',
        'error_foto_diperlukan' => 'At least one photo is required.',
    ],

    // Review Modal
    'modal_penilaian' => [
        'judul' => 'Completion Details',
        'judul_pesanan' => 'Order Title',
        'nomor_pesanan' => 'Order Number',
        'nama_klien' => 'Client Name',
        'lokasi' => 'Location',
        'tgl_pesan' => 'Order Date',
        'tgl_selesai' => 'Completion Date',
        'mulai_kerja' => 'Work Started',
        'selesai_kerja' => 'Work Finished',
        'total' => 'Total',
        'penilaian_heading_baru' => 'Lets give a rating for the Client!',
        'penilaian_heading_sudah' => 'This is your review for the Client',
        'label_komentar' => 'Comment',
        'placeholder_komentar' => 'Write your comment here...',
        'tombol_kirim' => 'Submit',
        'atau' => 'Or',
        'laporkan_masalah' => 'Report a problem',
    ],
    
    // Report Modal
    'modal_laporan' => [
        'judul' => 'Report',
        'upload_bukti' => 'Upload Proof (Image, max 5MB per image):',
        'keluh_kesah' => 'Your Complaint',
        'placeholder_keluh_kesah' => 'Describe the problem you experienced...',
        'tombol_kirim' => 'Send Report',
    ],

    // JavaScript Messages (These keys are accessed directly by JavaScript)
    'js_messages' => [
        'laporkan_masalah' => 'Report a problem',
        'laporan_sudah_terkirim' => 'Report has been sent',
        'pilih_rating_dulu' => 'Please select a rating first.',
        'isi_komentar_dulu' => 'Please fill in the comment.',
        'mengirim' => 'Submitting...',
        'mengirim_laporan' => 'Sending Report...',
        'upload_bukti_dulu' => 'At least one photo is required.',
        'isi_keluhan_dulu' => 'Please describe your complaint first.',
        'file_harus_gambar' => 'The uploaded file must be an image.',
        'ukuran_file_maksimal' => 'The report photo size cannot exceed 5 MB.',
        'maksimal_upload_gambar' => 'A maximum of :max report photos can be uploaded.',
        'kesalahan_umum' => 'An error occurred. Please try again.',
        'memproses' => 'Processing...',
    ],
];