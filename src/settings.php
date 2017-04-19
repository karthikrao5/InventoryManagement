<?php
return [
    'settings' => [
        'serverName' => 'cos-iv',
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => true,
        // View settings
        'view' => [
            'template_path' => __DIR__ . '/templates',
            'twig' => [
                // 'cache' => __DIR__ . '/cache/twig',
                // 'debug' => true,
                // 'auto_reload' => true,
                'cache' => false
            ],
        ],
        'logger' => [
            'name' => 'slim-app',
            'level' => Monolog\Logger::DEBUG,
            // 'path' => __DIR__ . '/log/app.log',
            'debugPath' => __DIR__.'/../../log/slimDebug.log',
            'errorPath' => __DIR__."/../../log/slimError.log",
            'infoPath' => __DIR__."/../../log/slimInfo.log"
        ],
        'jwtSecretKey' => 'someprivatekey',
        'encryptionAlgo' => 'HS256',
        'CAS-group-name' => "inventory-management-group",
        'token-expiration-time' => 3600,
    ],
];