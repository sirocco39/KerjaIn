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
        'workTitleLabel' => [
            'required' => 'The job title cannot be empty.',
        ],
        // Pesan error khusus untuk perbandingan waktu
        'datetime' => [
            'after_start_time' => 'The end time must be after the start time.'
        ],
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

];