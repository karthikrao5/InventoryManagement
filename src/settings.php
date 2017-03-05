<?php
return [
    'settings' => [
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => true,
        // View settings
        'view' => [
            'template_path' => __DIR__ . '/../src/templates',
            // 'twig' => [
            //     'cache' => __DIR__ . '/cache/twig',
            //     'debug' => true,
            //     'auto_reload' => true,
            // ],
        ],
        'logger' => [
            'name' => 'slim-app',
            'level' => Monolog\Logger::DEBUG,
            'path' => __DIR__ . '/log/app.log',
        ],
    ],
];