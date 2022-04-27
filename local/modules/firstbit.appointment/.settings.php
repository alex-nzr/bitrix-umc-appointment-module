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
            'firstbit.appointment.oneCReader' => [
                'constructor' => static function () {
                    return new OneCReader();
                },
            ],
            'firstbit.appointment.oneCWriter' => [
                'className' => OneCWriter::class,
                'constructorParams' => static function (){
                    return [];
                },
            ],
            'firstbit.appointment.mailerService' => [
                'className' => MailerService::class,
                'constructorParams' => static function (){
                    return [];
                },
            ],
            'firstbit.appointment.smsService' => [
                'className' => SmsService::class,
                'constructorParams' => static function (){
                    return [];
                },
            ],
        ],
        'readonly' => true,
    ],
];