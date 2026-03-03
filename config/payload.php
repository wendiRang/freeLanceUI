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
    | Class Map
    |--------------------------------------------------------------------------
    | Maps the "Class" field in payload.json to a supplier key below.
    | Add a new entry here whenever you add a new airline class.
    |
    */
    'class_map' => [
        'VietJetAir' => 'vietjetair',  // must match "Class" value in payload.json exactly
        'JejuAir'    => 'jejuair',
    ],

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
        'jejuair' => [
            'name'      => 'Jeju Air',
            'oneway'    => '/jejuair/puppeteer/oneway',
            'roundtrip' => '/jejuair/puppeteer/roundtrip',
        ],
    ],

];
