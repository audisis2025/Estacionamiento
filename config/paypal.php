<?php
return [
    'mode'          => env('PAYPAL_MODE', 'sandbox'),
    'client_id'     => env('PAYPAL_SANDBOX_CLIENT_ID'),
    'client_secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET'),
    'currency'      => env('PAYPAL_CURRENCY', 'MXN'),
];
