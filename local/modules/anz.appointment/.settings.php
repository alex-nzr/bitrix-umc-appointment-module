<?php

use ANZ\Appointment\Service\Container;
use ANZ\Appointment\Service\Message\Sms;
use ANZ\Appointment\Service\Message\Mailer;
use ANZ\Appointment\Service\OneC\Reader;
use ANZ\Appointment\Service\OneC\Writer;

return [
    'controllers' => [
        'value' => [
            'defaultNamespace' => '\\ANZ\\Appointment\\Controller',
        ],
        'readonly' => true,
    ],
    'services' => [
        'value' => [
            'anz.appointment.service.container'  => [
                'className' => Container::class,
            ]
        ],
        'readonly' => true,
    ],
];