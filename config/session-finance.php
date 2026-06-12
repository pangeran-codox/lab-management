<?php

return [
    'driver'          => 'database',
    'connection'      => 'finance',
    'table'           => 'finance_sessions',
    'lifetime'        => 120,
    'expire_on_close' => false,
    'encrypt'         => false,
    'files'           => storage_path('framework/sessions'),
    'cookie'          => 'finance_session',
    'path'            => '/finance',
    'domain'          => env('SESSION_DOMAIN', null),
    'secure'          => env('SESSION_SECURE_COOKIE', false),
    'http_only'       => true,
    'same_site'       => 'lax',
    'partitioned'     => false,
];
