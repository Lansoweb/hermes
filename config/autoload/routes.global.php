<?php

return [
    'dependencies' => [
        'invokables' => [
            Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\FastRouteRouter::class,
        ],
    ],

    'routes' => [
        [
            'name' => 'key.get',
            'path' => '/key/{key:[a-zA-z0-9]{1}[a-zA-z0-9-./]*[a-zA-z0-9]{1}}',
            'middleware' => Hermes\Action\GetAction::class,
            'allowed_methods' => ['GET'],
        ],
        [
            'name' => 'key.post',
            'path' => '/key/{key:[a-zA-z0-9]{1}[a-zA-z0-9-./]*[a-zA-z0-9]{1}}',
            'middleware' => Hermes\Action\CreateAction::class,
            'allowed_methods' => ['POST'],
        ],
        [
            'name' => 'key.patch',
            'path' => '/key/{key:[a-zA-z0-9]{1}[a-zA-z0-9-./]*[a-zA-z0-9]{1}}',
            'middleware' => Hermes\Action\UpdateAction::class,
            'allowed_methods' => ['PATCH','PUT'],
        ],
        [
            'name' => 'key.delete',
            'path' => '/key/{key:[a-zA-z0-9]{1}[a-zA-z0-9-./]*[a-zA-z0-9]{1}}',
            'middleware' => Hermes\Action\DeleteAction::class,
            'allowed_methods' => ['DELETE'],
        ],
    ],
];
