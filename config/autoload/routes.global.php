<?php

return [
    'dependencies' => [
        'invokables' => [
            Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\FastRouteRouter::class,
        ],
    ],

    'routes' => [
        [
            'name' => 'home',
            'path' => '/',
            'middleware' => Hermes\Action\HomePageAction::class,
            'allowed_methods' => ['GET'],
        ],
        [
            'name' => 'config.get',
            'path' => '/config/{service}[/{version:\d+}]',
            'middleware' => Hermes\Action\GetAction::class,
            'allowed_methods' => ['GET'],
        ],
        [
            'name' => 'config.post',
            'path' => '/config/{service}[/{version:\d+}]',
            'middleware' => Hermes\Action\PostAction::class,
            'allowed_methods' => ['POST'],
        ],
    ],
];
