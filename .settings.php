<?php

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\UI\EntitySelector\UserProvider;

try
{
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
                        'entityId' => class_exists(UserProvider::class) ? UserProvider::ENTITY_ID : 'user-default',
                        'provider' => [
                            'moduleId' => GetModuleID(__FILE__) ?? Configuration::getModuleId(),
                            'className' => UserProvider::class,
                        ],
                    ]
                ]
            ],
            'readonly' => true,
        ]
    ];
}
catch (Throwable $e)
{
    return [];
}