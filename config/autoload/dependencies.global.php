<?php

return [
    'dependencies' => [
        'invokables' => [
        ],
        'factories' => [
            Zend\Expressive\Application::class => Zend\Expressive\Container\ApplicationFactory::class,
        ],
        'abstract_factories' => [
            Demeter\Action\GetAction::class => Demeter\Action\ConfigFactory::class,
            Demeter\Action\CreateAction::class => Demeter\Action\ConfigFactory::class,
            Demeter\Action\DeleteAction::class => Demeter\Action\ConfigFactory::class,
            Demeter\Action\SetHeader::class => Demeter\Action\ConfigFactory::class,
        ],
    ]
];
