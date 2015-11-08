<?php

return [
    'dependencies' => [
        'invokables' => [
        ],
        'factories' => [
            Hermes\Action\HomePageAction::class => Hermes\Action\HomePageFactory::class,
            Zend\Expressive\Application::class => Zend\Expressive\Container\ApplicationFactory::class,
        ],
        'abstract_factories' => [
            Hermes\Action\GetAction::class => Hermes\Action\ConfigFactory::class,
            Hermes\Action\PostAction::class => Hermes\Action\ConfigFactory::class,
        ],
    ]
];
