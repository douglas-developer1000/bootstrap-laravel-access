<?php

return [
    'timeout' => [
        'token' => env('REGISTER_EXPIRATION_TOKEN_HOURS', 2),
        'email' => env('REGISTER_EXPIRATION_EMAIL_MINUTES', 15)
    ],
];
