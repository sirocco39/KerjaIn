<?php

return [
    // join.blade.php (Step 1)
    'personal_data' => 'Personal Data',
    'full_name' => 'Full Name',
    'first_name_placeholder' => 'First name',
    'last_name_placeholder' => 'Last name',
    'birthdate' => 'Birthdate',
    'birthdate_placeholder' => 'Date of birth',
    'gender' => 'Gender',
    'male' => 'Male',
    'female' => 'Female',
    'phone_number' => 'Phone Number',
    'phone_number_placeholder' => 'Phone number',
    'nik_number' => 'KTP Number', // KTP is Indonesian ID Card
    'nik_placeholder' => 'KTP/NIK Number',
    'domicile_address' => 'Domicile Address',
    'address_placeholder' => 'Domicile address',
    'next_button' => 'Next',

    // join2.blade.php (Step 2 - Contract Details)
    'contract_details' => 'Contract Details',
    'general_requirements_title' => 'General Requirements',
    'general_requirements_list' => [
        '- Applicants must be Indonesian citizens (WNI) and at least 17 years old.',
        '- Possess valid official identification, such as KTP, SIM, or Passport.',
        '- Must provide valid, accurate, and verifiable personal data.',
        '- Willing to follow all registration and verification procedures from Kerjain.',
        '- Not currently serving a criminal sentence or involved in illegal activities.',
    ],
    'account_and_verification_title' => 'Account and Verification',
    'account_and_verification_list' => [
        '- Applicants must have a registered and verified account on the Kerjain platform.',
        '- One person is only allowed to have one account as a worker.',
        '- The account is personal and may not be transferred or used by other parties.',
        '- Kerjain reserves the right to request additional documents for identity verification if needed.',
        '- All activities within the account are the full responsibility of the account owner.',
    ],
    'commitment_and_work_ethic_title' => 'Commitment and Work Ethic',
    'commitment_and_work_ethic_list' => [
        '- Workers are required to complete every accepted job in accordance with the description and agreed time.',
        '- It is forbidden to cancel work unilaterally without clear reasons or prior notice.',
        '- Workers are expected to show professional, friendly, and polite attitudes when interacting with service users.',
        '- It is forbidden to commit acts leading to fraud, harassment, violence, or other unlawful acts.',
        '- Kerjain reserves the right to review and suspend worker accounts if work ethic violations are found.',
    ],
    'payment_system_title' => 'Payment System',
    'payment_system_list' => [
        '- Wages or compensation for work are given according to the agreement stated in the job details.',
        '- Payment methods can be cash, bank transfer, or digital wallet, as agreed upon by both parties.',
        '- Workers are responsible for managing income and applicable tax obligations.',
        '- Kerjain may charge certain service fees or deductions, which will be transparently informed.',
    ],
    'responsibility_and_risk_title' => 'Responsibility and Risk',
    'responsibility_and_risk_list' => [
        '- Workers are fully responsible for the results of their work and its impact on service users.',
        '- Kerjain is not responsible for losses, accidents, or conflicts that occur outside the platform.',
        '- In the event of a dispute, workers are expected to resolve it wisely and can contact the Kerjain support team.',
        '- Workers must maintain the security of service users\' personal data and not disseminate it without permission.',
    ],
    'termination_of_cooperation_title' => 'Termination of Cooperation',
    'termination_of_cooperation_list' => [
        '- Kerjain reserves the right to temporarily or permanently deactivate worker accounts if violations of these terms and conditions are found.',
        '- Workers can delete their account or resign at any time through account settings.',
        '- Under certain conditions, Kerjain may conduct periodic evaluations of worker performance and ethics.',
    ],
    'terms_changes_title' => 'Changes to Terms',
    'terms_changes_list' => [
        '- Kerjain may update or change these terms and conditions at any time without direct notice.',
        '- Changes will be informed through the application, website, or official email.',
        '- Workers are deemed to have agreed to the changes if they continue to use the service after the update.',
    ],
    'i_agree_to_terms' => 'I agree to the ',
    'terms_and_conditions_link' => 'Kerjain Terms and Conditions',
    'i_agree_to_data_usage' => 'I agree for my data to be used for verification and security purposes',
    'continue_button' => 'Continue',


    // join3.blade.php (Step 3 - Verification & Payment Account)
    'verification_title' => 'Verification',
    'upload_selfie_photo' => 'Upload Selfie Photo',
    'max_file_size_info' => 'Max 5 MB, PNG, JPEG',
    'browse_file' => 'Browse File',
    'upload_id_card_photo' => 'Upload KTP Photo',
    'upload_selfie_with_id_card' => 'Upload Selfie with KTP Photo',
    'payment_account' => 'Payment Account',
    'account_holder_name' => 'Account Holder Name',
    'account_holder_name_placeholder' => 'Account holder name',
    'account_number' => 'Account Number',
    'account_number_placeholder' => 'Account number',
    'save_and_verify' => 'Save and Verify',
    'scanning_nik_info' => 'Scanning NIK from KTP, please wait...',
    'upload_ktp_to_scan_nik' => 'Upload KTP photo to scan NIK.',
    'error_processing_image' => 'An error occurred while processing the image. Please try again.',

    // pending.blade.php (now success.blade.php)
    'registration_successful' => 'Registration Successful!',
    'thanks_for_registering' => 'Thank you for registering. Your request has been successfully submitted.',
    'team_will_process_data' => 'Our team will soon process your data verification. This process may take <strong>1-3 business days</strong>. You will receive a notification via WhatsApp or email once the verification process is complete.',

    'verification_submission_received' => 'Verification Submission Received!',
    'thanks_for_submitting_verification' => 'Thank you for submitting your identity verification to become a Kerjain Partner.',
    'processing_submission_time' => 'Our team is processing your verification submission. This process usually takes <strong>1–3 business days</strong>. You will receive a notification via WhatsApp or email once the verification process is complete.',
    'back_to_homepage' => 'Back to Homepage',
    'questions_contact_us_at' => 'If you have any questions, please contact us at',

    // join-template.blade.php

    'hero_banner_text' => 'Come on, be a part of <span class="text-success"> Kerjain </span> – where we work together, help together, and earn together.',
    'become_part_of_kerjain' => 'Become part of',
    'personal_data_step' => 'Personal Data',
    'contract_details_step' => 'Contract Details',
    'verification_step' => 'Verification',

];
