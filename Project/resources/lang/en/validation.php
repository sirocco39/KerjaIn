<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute field must be accepted.',
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute field must be a string.',
    'numeric' => 'The :attribute field must be a number.',
    'date' => 'The :attribute field must be a valid date.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
    ],
    'max' => [
        'string' => 'The :attribute may not be greater than :max characters.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */
    'custom' => [
        'workStartDateLabel' => [
            'past_utc' => 'Start time cannot be in the past (UTC).',
        ],
        'datetime' => [
            'after_start_time' => 'The end time must be after the start time.',
        ],
        // Validation messages for file uploads from WorkerRegistrationController
        'photo_url' => [
            'required' => 'Selfie Photo is required.',
            'image' => 'The file must be an image.',
            'mimes' => 'Selfie Photo must be in JPEG, PNG, or JPG format.',
            'max' => 'Selfie Photo size must not exceed :max MB.',
        ],
        'id_card_url' => [
            'required' => 'ID Card Photo is required.',
            'image' => 'The file must be an image.',
            'mimes' => 'ID Card Photo must be in JPEG, PNG, or JPG format.',
            'max' => 'ID Card Photo size must not exceed :max MB.',
        ],
        'selfie_with_id_card_url' => [
            'required' => 'Selfie with ID Card Photo is required.',
            'image' => 'The file must be an image.',
            'mimes' => 'Selfie with ID Card Photo must be in JPEG, PNG, or JPG format.',
            'max' => 'Selfie with ID Card Photo size must not exceed :max MB.',
        ],
        'account_name' => [
            'required' => 'Account Holder Name is required.',
            'string' => 'Account Holder Name must be text.',
            'max' => 'Account Holder Name must not exceed :max characters.',
        ],
        'account_number' => [
            'required' => 'Account Number is required.',
            'string' => 'Account Number must be text.',
            'max' => 'Account Number must not exceed :max characters.',
        ],
        'agree_terms' => [
            'accepted' => 'You must agree to the Terms and Conditions.',
        ],
        'agree_data_usage' => [
            'accepted' => 'You must agree to the data usage for verification and security.',
        ],
        'first_name' => [
            'regex' => 'First name can only contain letters, spaces, hyphens, or periods.',
        ],
        'last_name' => [
            'regex' => 'Last name can only contain letters, spaces, hyphens, or periods.',
        ],
        'nik' => [
            'digits' => 'NIK must be 16 digits long.',
            'unique' => 'NIK is already registered.',
        ],
        'phone_number' => [
            'regex' => 'Please enter a valid phone number in 08XXXXXXXXXX format.',
            'unique' => 'Phone number is already registered.',
        ],
        'birthdate' => [
            'before_or_equal' => 'Age must be at least 17 years old.',
        ],
        'workTitleLabel' => [
            'required' => 'The job title cannot be empty.',
        ],
        // ... other custom validation rules ...
    ],


    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'workTitleLabel' => 'Job Title',
        'workDetailLabel' => 'Job Details',
        'workPriceLabel' => 'Wage',
        'workAddressLabel' => 'Address',
        'workStartDateLabel' => 'Start Date',
        'workEndDateLabel' => 'End Date',
        'workStartTimeLabel' => 'Start Time',
        'workEndTimeLabel' => 'End Time',
    ],

    // ... (pesan validasi bawaan Laravel lainnya, biarkan saja)

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Messages for JavaScript
    |--------------------------------------------------------------------------
    | These messages are specifically for frontend JavaScript validation.
    */
    'custom_js' => [
        'firstname_required' => 'First name is required.',
        'lastname_required' => 'Last name is required.',
        'email_required' => 'Email is required.',
        'email_invalid' => 'Please enter a valid email address.',
        'password_required' => 'Password is required.',
        'password_min_length' => 'Password must be at least 8 characters long.',
        'password_one_uppercase' => 'Password must contain at least one uppercase letter.',
        'password_one_lowercase' => 'Password must contain at least one lowercase letter.',
        'password_one_number' => 'Password must contain at least one number.',
        'password_one_symbol' => 'Password must contain at least one symbol.',
        'confirm_password_required' => 'Confirm password is required.',
        'confirm_password_match' => 'Passwords do not match.',
        'otp_required' => 'OTP is required.',
        'otp_format' => 'OTP must be a 6-digit number.',
    ],


];
