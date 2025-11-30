<?php

return [
    'masking' => [
        'keys' => [
            'password',
            'password_confirmation',
            'current_password',
            'token',
            'authorization',
            'api_key',
            'secret',
            'otp',
            'two_factor_secret',
            'two_factor_recovery_codes',
            'two_factor_backup_codes',
            'remember_token',
            'email',
            'phone',
            'mobile',
            'no_hp',
            'nik',
            'ktp',
            'alamat',
        ],
        'patterns' => [
            'phone' => '/\\+?\\d{9,15}/',
            'national_id' => '/\\b\\d{12,20}\\b/',
        ],
        'token_min_length' => 16,
    ],
    'sanitization' => [
        'skip_keys' => [
            'password',
            'password_confirmation',
            'photo',
            'avatar',
            'file',
            'attachment',
            'lampiran',
        ],
    ],
    'backup' => [
        'output_path' => storage_path('app/backups'),
        'chunk' => 500,
        'tables' => [
            'users' => [
                'email',
                'name',
                'password',
                'remember_token',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_backup_codes',
            ],
            'employees' => [
                'nama',
                'email',
                'phone',
                'alamat',
                'nik',
            ],
            'location_change_requests' => [
                'reason',
            ],
        ],
    ],
];
