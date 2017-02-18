<?php

// | doctrine-odm  ->  The driver to be used by the facilitator
// | Helper   ->  Class name helper. The helper is responsible
// |                   for opening the connection to the database


return [
    'boot-database' => true,
    'doctrine-odm' => [
        'helper' => \App\Helper\Database\Driver\MongoODM::class,
        'connection' => [
            'user' => '',
            'password' => '',
            'server' => '127.0.0.1',
            'dbname' => 'inventorytracking',
            'port' => '27017',
        ],
        'configuration' => [
            'ProxyDir' =>  __DIR__ . '/../src/Helper/odmcache/Proxy/',
            'HydratorsDir' => __DIR__ . '/../src/Helper/odmcache/Hydrators/',
            'Models' => __DIR__ . '/../src/Models/',
        ]
    ]
];