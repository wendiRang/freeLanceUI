<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Node Server URL
    |--------------------------------------------------------------------------
    | The base URL of the running node scraper server.
    | Set NODE_SERVER_URL in your .env file.
    |
    */
    'node_server_url' => env('NODE_SERVER_URL', 'http://localhost:3000'),

    /*
    |--------------------------------------------------------------------------
    | Suppliers
    |--------------------------------------------------------------------------
    | Each supplier maps to a node server route for oneway and roundtrip.
    | Add more suppliers here as you grow.
    |
    */
    'suppliers' => [
        'vietjetair' => [
            'name'      => 'VietJet Air',
            'oneway'    => '/vietjetair/puppeteer/oneway',
            'roundtrip' => '/vietjetair/puppeteer/roundtrip',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Payload
    |--------------------------------------------------------------------------
    | Pre-filled values shown in the monitor form on load.
    |
    */
    'default_payload' => [
        'origin' => 'SGN',
        'dest'   => 'HAN',
        'adult'  => 1,
        'child'  => 0,
        'infant' => 0,
        'proxy'  => null,
    ],

];
