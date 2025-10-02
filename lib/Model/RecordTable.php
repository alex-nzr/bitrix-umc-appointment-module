<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Model;

use ANZ\Appointment\Model\EntityObject\Record;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\ORM\Fields\Field;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\Type;
use Bitrix\Main\UserTable;

/**
 * @method static EntityObject\Record createObject($setDefaultValues = true)
 * @method static EntityObject\Record wakeUpObject($row)
 */
class RecordTable extends Model
{
    const FIELD_NAME_ID = 'ID';
    const FIELD_NAME_UID = 'XML_ID';
    const FIELD_NAME_DATE_CREATE = 'DATE_CREATE';
    const FIELD_NAME_CLINIC_TITLE = 'CLINIC_TITLE';
    const FIELD_NAME_SPECIALTY = 'SPECIALTY';
    const FIELD_NAME_DOCTOR_NAME = 'DOCTOR_NAME';
    const FIELD_NAME_SERVICE_TITLE = 'SERVICE_TITLE';
    const FIELD_NAME_DATETIME_VISIT = 'DATETIME_VISIT';
    const FIELD_NAME_PATIENT_NAME = 'PATIENT_NAME';
    const FIELD_NAME_PATIENT_PHONE = 'PATIENT_PHONE';
    const FIELD_NAME_PATIENT_EMAIL = 'PATIENT_EMAIL';
    const FIELD_NAME_STATUS_1C = 'STATUS_1C';
    const FIELD_NAME_COMMENT = 'COMMENT';
    const FIELD_NAME_DAYS_LEFT = 'DAYS_LEFT';
    const FIELD_NAME_USER_ID = 'USER_ID';

    public static function getTableName(): string
    {
        return "anz_appointment_record";
    }

    /**
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap(): array
    {
        $fields = [
            (new IntegerField(static::FIELD_NAME_ID))
                ->configureTitle(Loc::getMessage('ANZ_APPOINTMENT_TABLE_ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new StringField(static::FIELD_NAME_UID))
                ->configureTitle(Loc::getMessage('ANZ_APPOINTMENT_TABLE_XML_ID')),

            (new DatetimeField(static::FIELD_NAME_DATE_CREATE))
                ->configureTitle(Loc::getMessage('ANZ_APPOINTMENT_TABLE_DATE_CREATE'))
                ->configureRequired()
                ->configureDefaultValue(new Type\DateTime),

            (new StringField(static::FIELD_NAME_CLINIC_TITLE))
                ->configureTitle(Loc::getMessage('ANZ_APPOINTMENT_TABLE_CLINIC_TITLE'))
                ->configureRequired(),

            (new StringField(static::FIELD_NAME_SPECIALTY))
                ->configureTitle(Loc::getMessage('ANZ_APPOINTMENT_TABLE_SPECIALTY'))
                ->configureRequired(),

            (new StringField(static::FIELD_NAME_DOCTOR_NAME))
                ->configureTitle(Loc::getMessage('ANZ_APPOINTMENT_TABLE_DOCTOR_NAME'))
                ->configureRequired(),

            (new StringField(static::FIELD_NAME_SERVICE_TITLE))
                ->configureTitle(Loc::getMessage('ANZ_APPOINTMENT_TABLE_SERVICE_TITLE'))
                ->configureRequired(),

            (new DatetimeField(static::FIELD_NAME_DATETIME_VISIT))
                ->configureTitle(Loc::getMessage('ANZ_APPOINTMENT_TABLE_DATETIME_VISIT'))
                ->configureRequired(),

            (new StringField(static::FIELD_NAME_PATIENT_NAME))
                ->configureTitle(Loc::getMessage('ANZ_APPOINTMENT_TABLE_PATIENT_NAME'))
                ->configureRequired(),

            (new StringField(static::FIELD_NAME_PATIENT_PHONE))
                ->configureTitle(Loc::getMessage('ANZ_APPOINTMENT_TABLE_PATIENT_PHONE'))
                ->configureRequired(),

            (new StringField(static::FIELD_NAME_PATIENT_EMAIL))
                ->configureTitle(Loc::getMessage('ANZ_APPOINTMENT_TABLE_PATIENT_EMAIL')),


            (new StringField(static::FIELD_NAME_STATUS_1C))
                ->configureTitle(Loc::getMessage('ANZ_APPOINTMENT_TABLE_STATUS_1C')),

            (new TextField(static::FIELD_NAME_COMMENT))
                ->configureTitle(Loc::getMessage('ANZ_APPOINTMENT_TABLE_COMMENT')),

            (new ExpressionField(static::FIELD_NAME_DAYS_LEFT,
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
            ))->configureTitle(Loc::getMessage('ANZ_APPOINTMENT_TABLE_DAYS_LEFT')),

            (new IntegerField(static::FIELD_NAME_USER_ID))
                ->configureTitle(Loc::getMessage('ANZ_APPOINTMENT_TABLE_USER_ID')),

            new Reference(
                "USER",
                UserTable::class,
                ["=this.USER_ID" => "ref.ID"]
            )
        ];

        return array_map(function(Field $field) {
            if ($field instanceof StringField)
            {
                return $field->addFetchDataModifier([static::class, 'clearFetchedString'])
                             ->addSaveDataModifier([static::class, 'clearStringBeforeSave']);
            }
            return $field;
        }, $fields);
    }

    /**
     * @return string
     */
    public static function getUfId(): string
    {
        return "UMC_RECORD";
    }

    /**
     * @return string
     */
    public static function getObjectClass(): string
    {
        return Record::class;
    }
}