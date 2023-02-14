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
            ],
            'anz.appointment.service.oneC.reader' => [
                'className' => Reader::class,
            ],
            'anz.appointment.service.oneC.writer' => [
                'className' => Writer::class,
            ],
            'anz.appointment.service.message.mailer' => [
                'className' => Mailer::class,
            ],
            'anz.appointment.service.message.sms' => [
                'className' => Sms::class,
            ],
        ],
        'readonly' => true,
    ],
];