<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines (English)
    |--------------------------------------------------------------------------
    */
    'required' => 'The :attribute field is required.',
    'string'   => 'The :attribute field must be a string.',
    'numeric'  => 'The :attribute field must be a number.',
    'date'     => 'The :attribute is not a valid date.',
    'min'      => [
        'numeric' => 'The :attribute must be at least :min.',
    ],
    'max'      => [
        'string' => 'The :attribute may not be greater than :max characters.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'workTitleLabel'     => 'Job Title',
        'workDetailLabel'    => 'Job Details',
        'workPriceLabel'     => 'Price',
        'workAddressLabel'   => 'Address',
        'workStartDateLabel' => 'Start date',
        'workEndDateLabel'   => 'End date',
        'workStartTimeLabel' => 'Start time',
        'workEndTimeLabel'   => 'End time',
        'datetime'           => 'Job time'
    ],

     /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */
    'custom' => [
        'datetime' => [
            'after_start_time' => 'The end time must be after the start time.',
        ],
    ],
];