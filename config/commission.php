<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Commission Configuration
    |--------------------------------------------------------------------------
    */
    'rate'             => env('PLATFORM_COMMISSION_RATE', 5.0), // percentage
    'min_amount'       => 10.00,   // minimum commission in LKR
    'settlement_days'  => 7,       // days after order before settlement
];
