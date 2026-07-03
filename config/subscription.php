<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Subscription Plans Configuration
    |--------------------------------------------------------------------------
    */
    'registration_fee' => env('BUSINESS_REGISTRATION_FEE', 2000),

    'plans' => [
        'starter' => [
            'name'         => 'Starter',
            'price'        => 2000,
            'upload_limit' => 50,
            'duration'     => 30, // days
            'features'     => [
                'Up to 50 food listings per month',
                'Basic analytics',
                'Email support',
                'QR pickup verification',
            ],
        ],
        'professional' => [
            'name'         => 'Professional',
            'price'        => 5000,
            'upload_limit' => 250,
            'duration'     => 30,
            'features'     => [
                'Unlimited food listings',
                'Advanced analytics',
                'Priority support',
                'AI discount recommendations',
                'Featured badge eligible',
            ],
        ],
        'enterprise' => [
            'name'         => 'Enterprise',
            'price'        => 10000,
            'upload_limit' => -1,
            'duration'     => 30,
            'features'     => [
                'Unlimited food listings',
                'Multiple branches',
                'Advanced AI analytics',
                'Dedicated account manager',
                'Custom commission rate',
                'Homepage featured placement',
            ],
        ],
    ],

    'featured_promotions' => [
        'homepage'      => ['price' => 3000, 'duration' => 7],
        'search_top'    => ['price' => 2000, 'duration' => 7],
        'featured_badge'=> ['price' => 1000, 'duration' => 30],
    ],

    'expiry_warning_days' => [7, 1],
];
