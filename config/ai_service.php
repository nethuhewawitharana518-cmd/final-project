<?php

return [
    'base_url' => env('AI_SERVICE_URL', 'http://127.0.0.1:5000'),
    'timeout'  => env('AI_SERVICE_TIMEOUT', 30),

    'endpoints' => [
        'expiry_risk'       => '/ai/expiry-risk',
        'discount_recommend'=> '/ai/discount-recommend',
        'demand_forecast'   => '/ai/demand-forecast',
    ],

    'risk_thresholds' => [
        'high'   => 4,  // hours remaining
        'medium' => 12, // hours remaining
        // above 12 = low
    ],

    'discount_rules' => [
        ['hours' => 1,  'discount' => 75],
        ['hours' => 2,  'discount' => 60],
        ['hours' => 4,  'discount' => 40],
        ['hours' => 6,  'discount' => 20],
        ['hours' => 8,  'discount' => 15],
        ['hours' => 12, 'discount' => 10],
    ],
];
