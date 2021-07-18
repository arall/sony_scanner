<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Scanner Settings
    |--------------------------------------------------------------------------
    */

    'http' => [
        'timeout' => env('HTTP_TIMEOUT', 10),
        'verify' => env('HTTP_VERIFY_SSL', false),
    ],
    'process' => [
        'timeout' => env('PROCESS_TIMEOUT', 1800), # 30 min
    ]

];
