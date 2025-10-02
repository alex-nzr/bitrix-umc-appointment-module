<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 02.10.2025
 * ==================================================
*/

namespace ANZ\Appointment\Component\Appointment\ItemsList\Grid;

use ANZ\Appointment\Component\Appointment\ItemsList\Assembler\Field\EmailFieldAssembler;
use ANZ\Appointment\Component\Appointment\ItemsList\Assembler\Field\PhoneFieldAssembler;
use ANZ\Appointment\Component\Appointment\ItemsList\Assembler\Field\UserFieldAssembler;
use ANZ\Appointment\Component\Appointment\ItemsList\Provider\RowActionsProvider;
use ANZ\Appointment\Model\RecordTable;
use Bitrix\Main\Grid\Row\Assembler\Field\HtmlFieldAssembler;
use Bitrix\Main\Grid\Row\Assembler\Field\StringFieldAssembler;
use Bitrix\Main\Grid\Row\Assembler\OnlyFieldsRowAssembler;

class Rows extends \Bitrix\Main\Grid\Row\Rows
{
    public function __construct(array $columnIds)
    {
        $rowAssembler = new OnlyFieldsRowAssembler(
            $columnIds,
            new StringFieldAssembler($columnIds),
            new UserFieldAssembler([RecordTable::FIELD_NAME_USER_ID]),
            new HtmlFieldAssembler([RecordTable::FIELD_NAME_COMMENT]),
            new EmailFieldAssembler([RecordTable::FIELD_NAME_PATIENT_EMAIL]),
            new PhoneFieldAssembler([RecordTable::FIELD_NAME_PATIENT_PHONE])
        );

        parent::__construct($rowAssembler, new RowActionsProvider);
    }
}