<?php

return [
    'dependencies' => [
        'invokables' => [
        ],
        'factories' => [
            Zend\Expressive\Application::class => Zend\Expressive\Container\ApplicationFactory::class,
        ],
        'abstract_factories' => [
            Hermes\Action\GetAction::class => Hermes\Action\ConfigFactory::class,
            Hermes\Action\CreateAction::class => Hermes\Action\ConfigFactory::class,
            Hermes\Action\DeleteAction::class => Hermes\Action\ConfigFactory::class,
            Hermes\Action\SetHeaderMiddleware::class => Hermes\Action\ConfigFactory::class,
        ],
    ]
];
