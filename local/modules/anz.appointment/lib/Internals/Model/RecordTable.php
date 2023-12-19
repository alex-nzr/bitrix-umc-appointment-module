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

use ANZ\Appointment\Internals\Model\DataModifier\FetchModifier;
use ANZ\Appointment\Internals\Model\DataModifier\SaveModifier;
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
 *
 * @method static EO_Record_Query query()
 * @method static EO_Record_Result getByPrimary($primary, array $parameters = [])
 * @method static EO_Record_Result getById($id)
 * @method static EO_Record_Result getList(array $parameters = [])
 * @method static EO_Record_Entity getEntity()
 * @method static EO_Record_Collection createCollection()
 * @method static EO_Record_Collection wakeUpCollection($rows)
 * @method static Record createObject($setDefaultValues = true)
 * @method static Record wakeUpObject($row)
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

            (new StringField('XML_ID'))
                ->addFetchDataModifier([FetchModifier::class, 'clearFetchedString'])
                ->addSaveDataModifier([SaveModifier::class, 'clearStringBeforeSave']),

            (new DatetimeField('DATE_CREATE'))
                ->configureRequired()
                ->configureDefaultValue(new Type\DateTime),

            (new StringField('CLINIC_TITLE'))->configureRequired()
                ->addFetchDataModifier([FetchModifier::class, 'clearFetchedString'])
                ->addSaveDataModifier([SaveModifier::class, 'clearStringBeforeSave']),

            (new StringField('SPECIALTY'))->configureRequired()
                ->addFetchDataModifier([FetchModifier::class, 'clearFetchedString'])
                ->addSaveDataModifier([SaveModifier::class, 'clearStringBeforeSave']),

            (new StringField('DOCTOR_NAME'))->configureRequired()
                ->addFetchDataModifier([FetchModifier::class, 'clearFetchedString'])
                ->addSaveDataModifier([SaveModifier::class, 'clearStringBeforeSave']),

            (new StringField('SERVICE_TITLE'))->configureRequired()
                ->addFetchDataModifier([FetchModifier::class, 'clearFetchedString'])
                ->addSaveDataModifier([SaveModifier::class, 'clearStringBeforeSave']),

            (new DatetimeField('DATETIME_VISIT'))->configureRequired(),

            (new StringField('PATIENT_NAME'))->configureRequired()
                ->addFetchDataModifier([FetchModifier::class, 'clearFetchedString'])
                ->addSaveDataModifier([SaveModifier::class, 'clearStringBeforeSave']),

            (new StringField('PATIENT_PHONE'))->configureRequired()
                ->addFetchDataModifier([FetchModifier::class, 'clearFetchedString'])
                ->addSaveDataModifier([SaveModifier::class, 'clearStringBeforeSave']),

            (new StringField('PATIENT_EMAIL'))
                ->addFetchDataModifier([FetchModifier::class, 'clearFetchedString'])
                ->addSaveDataModifier([SaveModifier::class, 'clearStringBeforeSave']),

            (new TextField('COMMENT'))
                ->addFetchDataModifier([FetchModifier::class, 'clearFetchedString'])
                ->addSaveDataModifier([SaveModifier::class, 'clearStringBeforeSave']),

            (new StringField('STATUS_1C'))
                ->addFetchDataModifier([FetchModifier::class, 'clearFetchedString'])
                ->addSaveDataModifier([SaveModifier::class, 'clearStringBeforeSave']),

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