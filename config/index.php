<?php

return [
    'alert' => [
        'name' => 'alerts',
        'default_user' => env('ALERT_DEFAULT_USER_ID', 2),
        'max_count' => env('MAX_USER_ALERT', 10),
    ],
];
