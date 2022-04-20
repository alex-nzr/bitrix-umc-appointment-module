<?php

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
            'appointment.OneCReader' => [
                'constructor' => static function () {
                    return new OneCReader();
                },
            ],
            'appointment.OneCWriter' => [
                'className' => OneCWriter::class,
                'constructorParams' => static function (){
                    return [];
                },
            ],
            'appointment.MailerService' => [
                'className' => MailerService::class,
                'constructorParams' => static function (){
                    return [];
                },
            ],
            'appointment.SmsService' => [
                'className' => SmsService::class,
                'constructorParams' => static function (){
                    return [];
                },
            ],
        ],
        'readonly' => true,
    ],
];