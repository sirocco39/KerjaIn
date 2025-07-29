<?php

return [
    // join.blade.php (Step 1)
    'personal_data' => 'Data Pribadi',
    'full_name' => 'Nama Lengkap',
    'first_name_placeholder' => 'Nama depan',
    'last_name_placeholder' => 'Nama belakang',
    'birthdate' => 'Tanggal Lahir',
    'birthdate_placeholder' => 'Tanggal lahir',
    'gender' => 'Jenis Kelamin',
    'male' => 'Laki-laki',
    'female' => 'Perempuan',
    'phone_number' => 'Nomor Telepon',
    'phone_number_placeholder' => 'Nomor telepon',
    'nik_number' => 'Nomor KTP',
    'nik_placeholder' => 'Nomor KTP/NIK',
    'domicile_address' => 'Alamat Domisili',
    'address_placeholder' => 'Alamat domisili',
    'next_button' => 'Lanjut',

    // join2.blade.php (Step 2 - Contract Details)
    'contract_details' => 'Detail Kontrak',
    'general_requirements_title' => 'Persyaratan Umum',
    'general_requirements_list' => [
        '- Pendaftar merupakan Warga Negara Indonesia (WNI) dan berusia minimal 17 tahun.',
        '- Memiliki identitas resmi yang masih berlaku, seperti KTP, SIM, atau Paspor.',
        '- Wajib menyediakan data diri yang valid, akurat, dan dapat diverifikasi.',
        '- Bersedia mengikuti seluruh prosedur pendaftaran dan verifikasi dari KerjaIn.',
        '- Tidak sedang menjalani hukuman pidana atau terlibat dalam kegiatan yang melanggar hukum.',
    ],
    'account_and_verification_title' => 'Akun dan Verifikasi',
    'account_and_verification_list' => [
        '- Pendaftar wajib memiliki akun yang terdaftar dan terverifikasi di platform KerjaIn.',
        '- Satu orang hanya diperbolehkan memiliki satu akun sebagai pekerja.',
        '- Akun bersifat pribadi dan tidak boleh dipindahtangankan atau digunakan oleh pihak lain.',
        '- KerjaIn berhak meminta dokumen tambahan untuk verifikasi identitas jika dibutuhkan.',
        '- Segala aktivitas di dalam akun menjadi tanggung jawab penuh pemilik akun.',
    ],
    'commitment_and_work_ethic_title' => 'Komitmen dan Etika Kerja',
    'commitment_and_work_ethic_list' => [
        '- Pekerja wajib menyelesaikan setiap pekerjaan yang diterima sesuai dengan deskripsi dan waktu yang telah disepakati.',
        '- Dilarang membatalkan pekerjaan secara sepihak tanpa alasan yang jelas atau pemberitahuan sebelumnya.',
        '- Pekerja diharapkan menunjukkan sikap profesional, ramah, dan sopan dalam berinteraksi dengan pengguna jasa.',
        '- Dilarang melakukan tindakan yang mengarah pada penipuan, pelecehan, kekerasan, atau hal lain yang melanggar hukum.',
        '- KerjaIn berhak meninjau dan menangguhkan akun pekerja jika ditemukan pelanggaran etika kerja.',
    ],
    'payment_system_title' => 'Sistem Pembayaran',
    'payment_system_list' => [
        '- Upah atau imbalan atas pekerjaan diberikan sesuai kesepakatan yang dicantumkan dalam detail pekerjaan.',
        '- Metode pembayaran dapat berupa tunai, transfer bank, atau dompet digital, sesuai yang disetujui kedua belah pihak.',
        '- Pekerja bertanggung jawab terhadap pengelolaan penghasilan dan kewajiban perpajakan yang berlaku.',
        '- KerjaIn dapat mengenakan biaya layanan atau potongan tertentu, yang akan diinformasikan secara transparan.',
    ],
    'responsibility_and_risk_title' => 'Tanggung Jawab dan Risiko',
    'responsibility_and_risk_list' => [
        '- Pekerja bertanggung jawab penuh atas hasil pekerjaan dan dampaknya kepada pengguna jasa.',
        '- KerjaIn tidak bertanggung jawab atas kerugian, kecelakaan, atau konflik yang terjadi di luar platform.',
        '- Dalam hal terjadi sengketa, pekerja diharapkan menyelesaikannya dengan bijak dan dapat menghubungi tim dukungan KerjaIn.',
        '- Pekerja wajib menjaga keamanan data pribadi milik pengguna jasa dan tidak menyebarkannya tanpa izin.',
    ],
    'termination_of_cooperation_title' => 'Pemutusan Kerja Sama',
    'termination_of_cooperation_list' => [
        '- KerjaIn berhak menonaktifkan akun pekerja secara sementara atau permanen jika ditemukan pelanggaran terhadap syarat dan ketentuan ini.',
        '- Pekerja dapat menghapus akun atau mengundurkan diri kapan saja melalui pengaturan akun.',
        '- Dalam kondisi tertentu, KerjaIn dapat melakukan evaluasi berkala terhadap performa dan etika pekerja.',
    ],
    'terms_changes_title' => 'Perubahan Ketentuan',
    'terms_changes_list' => [
        '- KerjaIn dapat memperbarui atau mengubah syarat dan ketentuan ini sewaktu-waktu tanpa pemberitahuan langsung.',
        '- Perubahan akan diinformasikan melalui aplikasi, situs web, atau email resmi.',
        '- Pekerja dianggap telah menyetujui perubahan tersebut jika tetap menggunakan layanan setelah pembaruan dilakukan.',
    ],
    'i_agree_to_terms' => 'Saya setuju dengan ',
    'terms_and_conditions_link' => 'Syarat dan Ketentuan KerjaIn',
    'i_agree_to_data_usage' => 'Saya bersedia data saya digunakan untuk keperluan verifikasi dan keamanan',
    'continue_button' => 'Lanjut',

    // join3.blade.php (Step 3 - Verification & Payment Account)
    'verification_title' => 'Verifikasi',
    'upload_selfie_photo' => 'Upload Foto Diri',
    'max_file_size_info' => 'Max 5 MB, PNG, JPEG',
    'browse_file' => 'Browse File',
    'upload_id_card_photo' => 'Upload Foto KTP',
    'upload_selfie_with_id_card' => 'Upload Foto Diri dan KTP',
    'payment_account' => 'Akun Pembayaran',
    'account_holder_name' => 'Nama Pemilik Rekening',
    'account_holder_name_placeholder' => 'Nama pemilik rekening',
    'account_number' => 'Nomor Rekening',
    'account_number_placeholder' => 'Nomor rekening',
    'save_and_verify' => 'Simpan dan Verifikasi',
    'scanning_nik_info' => 'Memindai NIK dari KTP, mohon tunggu...',
    'upload_ktp_to_scan_nik' => 'Unggah foto KTP untuk memindai NIK.',
    'error_processing_image' => 'Terjadi kesalahan saat memproses gambar. Silakan coba lagi.',

    // pending.blade.php (now success.blade.php)
    'registration_successful' => 'Pendaftaran Berhasil!',
    'thanks_for_registering' => 'Terima kasih telah mendaftar. Permintaan Anda telah berhasil disubmit.',
    'team_will_process_data' => 'Tim kami akan segera memproses verifikasi data Anda. Proses ini mungkin memakan waktu <strong>1-3 hari kerja</strong>. Anda akan menerima notifikasi melalui WhatsApp atau email setelah proses verifikasi selesai.',

    'verification_submission_received' => 'Pengajuan Verifikasi Diterima!',
    'thanks_for_submitting_verification' => 'Terima kasih telah mengajukan verifikasi identitas untuk menjadi Mitra KerjaIn.',
    'processing_submission_time' => 'Tim kami sedang memproses pengajuan verifikasi Anda. Proses ini biasanya memakan waktu <strong>1–3 hari kerja</strong>. Anda akan menerima notifikasi melalui WhatsApp atau email setelah proses verifikasi selesai.',
    'back_to_homepage' => 'Kembali ke Beranda',
    'questions_contact_us_at' => 'Jika ada pertanyaan, silakan hubungi kami di',

    // join-template.blade.php
    'hero_banner_text' => 'Yuk, jadi bagian dari <span class="text-success"> KerjaIn </span>&nbsp;–&nbsp; tempat di mana kerja bareng, bantu bareng, cuan bareng.',
    'become_part_of_kerjain' => 'Jadi bagian dari ',
    'personal_data_step' => 'Data Pribadi',
    'contract_details_step' => 'Detail Kontrak',
    'verification_step' => 'Verifikasi',

];
