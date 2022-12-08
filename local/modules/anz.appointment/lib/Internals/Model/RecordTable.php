<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - RecordTable.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace ANZ\Appointment\Internals\Model;


use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\Type;
use Bitrix\Main\UserTable;

/**
 * Class RecordTable
 * @package ANZ\Appointment\Internals\Model
 */
class RecordTable extends DataManager
{
    public static function getTableName(): string
    {
        return "anz_appointment_record";
    }

    /**
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            new StringField('XML_ID'),

            (new DatetimeField('DATE_CREATE'))
                ->configureRequired()
                ->configureDefaultValue(new Type\DateTime),

            (new StringField('CLINIC_TITLE'))->configureRequired(),
            (new StringField('SPECIALTY'))->configureRequired(),
            (new StringField('DOCTOR_NAME'))->configureRequired(),
            (new StringField('SERVICE_TITLE'))->configureRequired(),
            (new DatetimeField('DATETIME_VISIT'))->configureRequired(),

            (new StringField('PATIENT_NAME'))->configureRequired(),
            (new StringField('PATIENT_PHONE'))->configureRequired(),
            new StringField('PATIENT_EMAIL'),

            new TextField('COMMENT'),

            new StringField('STATUS_1C'),

            new ExpressionField('DAYS_LEFT',
                'TIMESTAMPDIFF(DAY, NOW(), %s)', ['DATETIME_VISIT'],
                [
                    'fetch_data_modification' => function () {
                        return [
                            function ($value) {
                                return (int)$value >= 0 ? $value : 0;
                            }
                        ];
                    }
                ]
            ),

            new IntegerField("USER_ID"),

            new ReferenceField(
                "USER",
                UserTable::class,
                ["=this.USER_ID" => "ref.ID"]
            )
        ];
    }

    /**
     * @return string
     */
    public static function getUfId(): string
    {
        return "FB_APP_RECORD";
    }

    /**
     * @return string
     */
    public static function getObjectClass(): string
    {
        return Record::class;
    }
}