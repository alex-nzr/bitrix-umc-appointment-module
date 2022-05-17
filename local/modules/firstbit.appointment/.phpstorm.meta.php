<?php
namespace PHPSTORM_META
{
	registerArgumentsSet('firstbit_appointment_serviceLocator_codes',
		'firstbit.appointment.service.reader',
        'firstbit.appointment.service.writer',
        'firstbit.appointment.service.sms',
        'firstbit.appointment.service.mailer',
	);

	expectedArguments(\Bitrix\Main\DI\ServiceLocator::get(), 0, argumentsSet('firstbit_appointment_serviceLocator_codes'));

	override(\Bitrix\Main\DI\ServiceLocator::get(0), map([
        'firstbit.appointment.service.reader'   => \FirstBit\Appointment\Services\OneCReader::class,
        'firstbit.appointment.service.writer'   => \FirstBit\Appointment\Services\OneCWriter::class,
        'firstbit.appointment.service.sms'      => \FirstBit\Appointment\Services\SmsService::class,
        'firstbit.appointment.service.mailer'   => \FirstBit\Appointment\Services\MailerService::class,
	]));
}
