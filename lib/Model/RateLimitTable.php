<?php

namespace ANZ\Appointment\Model;

use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;

class RateLimitTable extends Model
{
    public static function getTableName(): string
    {
        return 'anz_appointment_rate_limit';
    }

    public static function getMap(): array
    {
        return [
            (new StringField('RATE_KEY'))
                ->configurePrimary()
                ->configureSize(64),
            (new IntegerField('REQUEST_COUNT'))
                ->configureRequired(),
            (new IntegerField('EXPIRES_AT'))
                ->configureRequired(),
        ];
    }
}
