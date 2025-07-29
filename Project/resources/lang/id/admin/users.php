<?php

return [
    // General / Common
    'total_users' => 'Total Pengguna',
    'active_today' => 'aktif hari ini',
    'new_this_week' => 'baru minggu ini',
    'total_workers' => 'Total Pekerja',
    'active_workers_today' => 'aktif hari ini',
    'blocked_users' => 'Pengguna Diblokir',
    'view_blocked_list' => 'Lihat daftar blokir',
    'reported_users' => 'Pengguna Dilaporkan',
    'view_report_list' => 'Lihat daftar laporan',
    'search_user_by_id_name' => 'Cari pengguna berdasarkan ID atau Nama...',
    'search_button' => 'Cari',
    'user_found' => 'Pengguna <strong>:name</strong> ditemukan.',
    'user_id' => 'ID: <strong>:id</strong>',
    'active_since' => 'Aktif :time_ago',
    'no_login_activity' => 'Belum ada aktivitas login.',
    'status_colon' => 'Status:',
    'status' => 'Status',
    'blocked' => 'Diblokir',
    'active' => 'Aktif',
    'user_not_found' => 'Pengguna dengan ID atau Nama <strong>:query</strong> tidak ditemukan.',
    'time' => 'Waktu',
    'user' => 'Pengguna',
    'target' => 'Target',
    'activity' => 'Aktivitas',
    'log_name' => 'Log Nama',
    'properties' => 'Properti',
    'no_activity_logs_for_user' => 'Tidak ada log aktivitas untuk pengguna ini.',
    'no_recent_activity_logs' => 'Tidak ada log aktivitas terbaru.',
    'name' => 'Nama',
    'email' => 'Email',
    'role' => 'Peran',
    'worker' => 'Pekerja',
    'regular_user' => 'Pengguna Biasa',
    'action' => 'Aksi',
    'view_activity' => 'Lihat Aktivitas',
    'no_users_found' => 'Tidak ada pengguna ditemukan.',
    'n_a' => 'N/A',

    // index.blade.php specific (User Activity Log)
    'recent_activity_logs' => 'Log Aktivitas Terbaru :for_user', // Will be "untuk [User Name]" or empty
    'user_activity_list' => 'Daftar aktivitas pengguna',
    'in_the_system' => 'di sistem.',
    'search_for_user_activity' => 'Cari pengguna berdasarkan ID atau Nama...',


    // all-list.blade.php specific (All Users List)
    'all_users_list' => 'Daftar Semua Pengguna',
    'search_all_users_placeholder' => 'Cari pengguna berdasarkan ID atau Nama...',


    // blocked-list.blade.php specific (Blocked Users List)
    'blocked_users_list' => 'Daftar Pengguna Diblokir',
    'search_blocked_users_placeholder' => 'Cari pengguna diblokir berdasarkan ID atau Nama...',
    'no_blocked_users_found' => 'Tidak ada pengguna yang diblokir.',
    'unblock' => 'Unblock',

    // user-activity-log.blade.php specific (Single User Activity Log)
    'activity_log_for' => 'Log Aktivitas untuk :user_name',
    'unblock_user' => 'Batal Blokir',
    'block_user' => 'Blokir Pengguna',
    'back_button' => 'Kembali',
    'description' => 'Deskripsi',

    // Modals
    'confirm_unblock_user' => 'Konfirmasi Batal Blokir Pengguna',
    'confirm_unblock_message' => 'Anda akan **membatalkan blokir** pengguna <strong id="unblockUserName"></strong>. Pengguna ini akan bisa login dan mengakses layanan kembali.',
    'are_you_sure_continue' => 'Apakah Anda yakin ingin melanjutkan?',
    'cancel' => 'Batal',
    'yes_unblock' => 'Ya, Batal Blokir',

    'confirm_block_user' => 'Konfirmasi Blokir Pengguna',
    'confirm_block_message' => 'Anda akan **memblokir** pengguna <strong id="blockUserName"></strong>. Pengguna ini tidak akan bisa login atau mengakses layanan.',
    'yes_block' => 'Ya, Blokir',

    // Worker List Specific
    'worker_list' => 'Daftar Pekerja',
    'search_workers_placeholder' => 'Cari pekerja berdasarkan ID atau Nama...',
    'rating' => 'Rating',
    'job_done' => 'Job Done',
    'no_workers_found' => 'Tidak ada pekerja ditemukan.',
    'worker_not_found' => 'Pekerja dengan ID atau Nama <strong>:query</strong> tidak ditemukan.',
];
