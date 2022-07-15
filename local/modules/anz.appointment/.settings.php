<?php

use ANZ\Appointment\Services\Container;
use ANZ\Appointment\Services\Message\SmsService;
use ANZ\Appointment\Services\Message\MailerService;
use ANZ\Appointment\Services\OneC\Reader;
use ANZ\Appointment\Services\OneC\Writer;

return [
    'controllers' => [
        'value' => [
            'defaultNamespace' => '\\ANZ\\Appointment\\Controllers',
        ],
        'readonly' => true,
    ],
    'services' => [
        'value' => [
            'anz.appointment.services.container'  => [
                'className' => Container::class,
            ],
            'anz.appointment.services.oneC.reader' => [
                'className' => Reader::class,
            ],
            'anz.appointment.services.oneC.writer' => [
                'className' => Writer::class,
            ],
            'anz.appointment.services.message.mailerService' => [
                'constructor' => static function () {
                    return new MailerService();
                },
            ],
            'anz.appointment.services.message.smsService' => [
                'className' => SmsService::class,
                'constructorParams' => static function (){
                    return [];
                },
            ],
        ],
        'readonly' => true,
    ],
];