<?php

return [
       'job_accepted_success_chat_redirect' => 'Job successfully accepted! You will be redirected to the chat page.',
    'job_marked_completed_success' => 'Job successfully marked as complete!',

    // Auth & Registration
    'login_berhasil' => 'Login successful! Welcome, :nama!',
    'logout_berhasil' => 'You have been successfully logged out.',
    'email_atau_kata_sandi_salah' => 'Incorrect email or password.',
    'akun_diblokir' => 'Your account has been blocked. Please contact the administrator.',
    'admin_selamat_datang' => 'Welcome, Admin :nama! Please choose your destination.',
    'otp_kadaluwarsa' => 'OTP has expired or has not been requested.',
    'otp_tidak_valid' => 'Invalid OTP.',
    'otp_sent_success' => 'OTP has been sent to your email. Please check your inbox.',
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
    'job_accepted_success_redirect' => 'Job successfully accepted! You will be redirected to the chat page.',

    // Finance & Top Up
    'gagal_membuat_invoice' => 'Failed to create payment invoice: :error',
    'topup_unauthorized_webhook' => 'Unauthorized.',
    'topup_no_pending_order_webhook' => 'No pending order found or already processed.',
    'topup_webhook_processed_success' => 'Webhook processed.',

    // Navigation & Roles
    'beralih_ke_requester' => 'You have successfully switched to the Job Requester role, :nama!',
    'beralih_ke_taker' => 'You have successfully switched to the Job Taker role, :nama!',
    'bahasa_diubah' => 'Language successfully changed to :locale.',

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
    'worker_profile_not_found' => 'Worker profile not found. Please login.',
    'unauthorized_view_transaction_details' => 'Unauthorized to view this transaction details.',

    // Messages from Admin Panel (Reports & Verifications)
    'admin_reports_verification_request_not_found' => 'Verification request not found.',
    'admin_reports_verification_request_already_processed' => 'Verification request has already been processed.',
    'admin_reports_verification_request_approved' => 'Verification request successfully approved.',
    'admin_reports_failed_to_approve_verification' => 'Failed to approve verification request.',
    'admin_reports_verification_request_rejected' => 'Verification request successfully rejected. Reason: :reason',
    'admin_reports_verification_request_not_pending' => 'This verification request is no longer in "pending" status.',
    'notification_status_updated_successfully' => 'Notification status updated successfully.',
    'notification_deleted_successfully' => 'Notification deleted successfully.',
    'admin_user_blocked_success' => 'User successfully blocked.',
    'admin_user_unblocked_success' => 'User successfully unblocked.',
    'error_loading_job_details' => 'An error occurred while loading job details. Please try again.',

      'not_authorized_to_view' => 'You are not authorized to view this page.',
    'not_authorized_to_start_job' => 'You are not authorized to start this job.',
    'job_not_in_accepted_status' => 'The job is not in "Accepted" status and cannot be started.',
    'job_started' => 'Job started.',
    'not_authorized_to_upload_proof' => 'You are not authorized to upload proof for this job.',
    'job_not_in_progress_status' => 'The job is not in "In Progress" status and cannot upload proof.',
    'validation_failed' => 'Validation failed.',
    'proof_uploaded_success' => 'Job proof successfully uploaded. Your job is now under review.',
    'error_uploading_proof' => 'An error occurred while uploading proof:',
    'not_authorized_to_mark_complete' => 'You are not authorized to mark this job as complete.',
    'job_marked_submitted_success' => 'Job successfully marked as complete and is awaiting client confirmation.',
    'job_status_not_allowed' => 'The job status does not allow it to be marked as complete.',
    'not_authorized_to_report' => 'You are not authorized to report this transaction.',
    'report_submitted_success' => 'Report successfully submitted and will be reviewed shortly.',
    'error_submitting_report' => 'An error occurred while submitting the report:',
    'not_authorized_to_review' => 'You are not authorized to give this review.',
    'review_already_given' => 'You have already given a review for this transaction.',
    'review_saved_success' => 'Your review has been successfully saved!',
    'error_saving_review' => 'An error occurred while saving the review:',
    'unauthorized_access_job_page' => 'Unauthorized access attempt to accepted job page',
    'unauthorized_report_attempt' => 'Unauthorized report attempt for transaction',
    'unauthorized_review_attempt' => 'Unauthorized review attempt for transaction by non-worker user.',

    // Status text for index method (should match your accepted-work-request.php or a dedicated status file)
    'status_accepted' => 'Accepted',
    'status_in_progress' => 'In Progress',
    'status_submitted' => 'Submitted',
    'status_completed' => 'Completed',
    'status_cancelled' => 'Cancelled',
];