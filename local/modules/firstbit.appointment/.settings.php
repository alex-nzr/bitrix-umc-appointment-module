<?php

use FirstBit\Appointment\Services\Container;
use FirstBit\Appointment\Services\SmsService;
use FirstBit\Appointment\Services\MailerService;
use FirstBit\Appointment\Services\OneCReader;
use FirstBit\Appointment\Services\OneCWriter;

return [
    'controllers' => [
        'value' => [
            'defaultNamespace' => '\\FirstBit\\Appointment\\Controllers',
        ],
        'readonly' => true,
    ],
    'services' => [
        'value' => [
            'firstBit.appointment.services.container'  => [
                'className' => Container::class,
            ],
            'firstBit.appointment.services.oneCReader' => [
                'className' => OneCReader::class,
            ],
            'firstBit.appointment.services.oneCWriter' => [
                'className' => OneCWriter::class,
            ],
            'firstBit.appointment.services.mailerService' => [
                'constructor' => static function () {
                    return new MailerService();
                },
            ],
            'firstBit.appointment.services.smsService' => [
                'className' => SmsService::class,
                'constructorParams' => static function (){
                    return [];
                },
            ],
        ],
        'readonly' => true,
    ],
];