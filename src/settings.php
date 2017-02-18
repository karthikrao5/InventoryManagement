<?php
return [
    'settings' => [
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => false,
        // View settings
        'view' => [
            'template_path' => __DIR__ . '/../src/templates',
            // 'twig' => [
            //     'cache' => __DIR__ . '/cache/twig',
            //     'debug' => true,
            //     'auto_reload' => true,
            // ],
        ],
        // monolog settings
        //'logger' => [
        //    'name' => 'app',
        //    'path' => __DIR__ . '/../src/log/app.log',
        //    'level' => \Monolog\Logger::DEBUG,
        //],
    ],
];