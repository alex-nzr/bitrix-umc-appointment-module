<?php

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\UI\EntitySelector\UserProvider;

return [
    'controllers' => [
        'value' => [
            'defaultNamespace' => '\\ANZ\\Appointment\\Controller',
            'namespaces' => [
                '\\ANZ\\Appointment\\Controller' => 'api',
            ],
        ],
        'readonly' => true,
    ],
    'ui.entity-selector' => [
        'value' => [
            'entities' => [
                [
                    'entityId' => UserProvider::ENTITY_ID,
                    'provider' => [
                        'moduleId' => Configuration::getModuleId(),
                        'className' => UserProvider::class,
                    ],
                ]
            ]
        ],
        'readonly' => true,
    ]
];