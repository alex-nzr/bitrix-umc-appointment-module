<?php

use FirstBit\Appointment\Services\Container;
use FirstBit\Appointment\Services\Message\SmsService;
use FirstBit\Appointment\Services\Message\MailerService;
use FirstBit\Appointment\Services\OneC\Reader;
use FirstBit\Appointment\Services\OneC\Writer;

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
            'firstBit.appointment.services.oneC.reader' => [
                'className' => Reader::class,
            ],
            'firstBit.appointment.services.oneC.writer' => [
                'className' => Writer::class,
            ],
            'firstBit.appointment.services.message.mailerService' => [
                'constructor' => static function () {
                    return new MailerService();
                },
            ],
            'firstBit.appointment.services.message.smsService' => [
                'className' => SmsService::class,
                'constructorParams' => static function (){
                    return [];
                },
            ],
        ],
        'readonly' => true,
    ],
];