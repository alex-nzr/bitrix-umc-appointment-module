<?php
/*
 * ==================================================
 * This file is part of project saint-gobain
 * 01.10.2025
 * ==================================================
*/
namespace ANZ\Appointment\Component\Appointment\ItemsList\Action\Row;

use ANZ\Appointment\Component\Appointment\ItemsList\Component;
use ANZ\Appointment\Model\RecordTable;
use Bitrix\Main\Grid\Row\Action\BaseAction;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;

class DeleteAction extends BaseAction
{
    public static function getId(): ?string
    {
        return 'delete';
    }

    protected function getText(): string
    {
        return Loc::getMessage('ANZ_APPOINTMENT_BTN_DELETE_TEXT');
    }

    public function processRequest(HttpRequest $request): ?Result
    {
        return null;
    }

    public function getControl(array $rawFields): ?array
    {
        $id = $rawFields[RecordTable::FIELD_NAME_ID];
        $uid = $rawFields[RecordTable::FIELD_NAME_UID];
        $gridId = Component::getGridId();

        $this->default = true;
        $this->onclick = 'confirm("'.Loc::getMessage('ANZ_APPOINTMENT_BTN_DELETE_TEXT').'?") 
                                ? BX.Anz?.Appointment?.Admin?.AppointmentList?.deleteAppointment('.$id.', "'.$gridId.'", "'.$uid.'") 
                                : void(0)';

        return parent::getControl($rawFields);
    }
}