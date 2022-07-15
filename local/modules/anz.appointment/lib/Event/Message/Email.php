<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - Email.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace ANZ\Appointment\Event\Message;


use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Internal\EventTypeTable;
use CEventMessage;
use CEventType;
use ANZ\Appointment\Config\Constants;

/**
 * Class Email
 * @package ANZ\Appointment\Event\Message
 */
class Email
{
    /**
     * @return int
     * @throws \Exception
     */
    public function createEmailNoteEvent(): int
    {
        $arFields = [
            "EVENT_TYPE"    => EventTypeTable::TYPE_EMAIL,
            "EVENT_NAME"    => Constants::EMAIL_NOTE_EVENT_CODE,
            "NAME"          => Loc::getMessage("ANZ_APPOINTMENT_EMAIL_NOTE_NAME"),
            "LID"           => 'ru',
            "DESCRIPTION"   =>  "#TEXT# - " . Loc::getMessage("ANZ_APPOINTMENT_NOTE_DESC_TEXT") . "\n" .
                "#EMAIL_TO# - " . Loc::getMessage("ANZ_APPOINTMENT_NOTE_DESC_EMAIL_TO")
        ];
        $result = EventTypeTable::add($arFields);
        return $result->getId();
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function createEmailConfirmEvent(): int
    {
        $arFields = [
            "EVENT_TYPE"    => EventTypeTable::TYPE_EMAIL,
            "EVENT_NAME"    => Constants::EMAIL_CONFIRM_EVENT_CODE,
            "NAME"          => Loc::getMessage("ANZ_APPOINTMENT_EMAIL_CONFIRM_NAME"),
            "LID"           => 'ru',
            "DESCRIPTION"   => "#CODE# - " . Loc::getMessage("ANZ_APPOINTMENT_CONFIRM_DESC_CODE")
        ];
        $result = EventTypeTable::add($arFields);
        return $result->getId();
    }

    /**
     * @param array $siteIds
     * @return int
     */
    public function createEmailNoteTemplate(array $siteIds): int
    {
        $params = [
            "ACTIVE"     => "Y",
            "EVENT_NAME" => Constants::EMAIL_NOTE_EVENT_CODE,
            "LID"        => $siteIds,
            "LANGUAGE_ID"=> 'ru',
            "EMAIL_FROM" => '#DEFAULT_EMAIL_FROM#',
            "EMAIL_TO"   => "#EMAIL_TO#",
            "BCC"        => "",
            "SUBJECT"    => Loc::getMessage("ANZ_APPOINTMENT_EMAIL_NOTE_NAME"),
            "BODY_TYPE"  => "text",
            "MESSAGE"    => "#TEXT#",
        ];
        $obTemplate = new CEventMessage;
        $id = $obTemplate->Add($params);
        return (int)$id;
    }

    /**
     * @param array $siteIds
     * @return int
     */
    public function createEmailConfirmTemplate(array $siteIds): int
    {
        $params = [
            "ACTIVE"     => "Y",
            "EVENT_NAME" => Constants::EMAIL_CONFIRM_EVENT_CODE,
            "LID"        => $siteIds,
            "LANGUAGE_ID"=> 'ru',
            "EMAIL_FROM" => '#DEFAULT_EMAIL_FROM#',
            "EMAIL_TO"   => "#EMAIL_TO#",
            "BCC"        => "",
            "SUBJECT"    => Loc::getMessage("ANZ_APPOINTMENT_CONFIRM_DESC_CODE"),
            "BODY_TYPE"  => "text",
            "MESSAGE"    => Loc::getMessage("ANZ_APPOINTMENT_CONFIRM_DESC_CODE") . " - #CODE#",
        ];
        $obTemplate = new CEventMessage;
        $id = $obTemplate->Add($params);
        return (int)$id;
    }

    public function deleteEmailEvents()
    {
        $obEventType = new CEventType;

        $obEventType->Delete(Constants::EMAIL_NOTE_EVENT_CODE);
        $obEventType->Delete(Constants::EMAIL_CONFIRM_EVENT_CODE);
    }

    public function deleteEmailTemplates(): void
    {
        $arFilter = [
            "TYPE_ID" => [
                Constants::EMAIL_CONFIRM_EVENT_CODE,
                Constants::EMAIL_NOTE_EVENT_CODE
            ]
        ];
        $by = "ID";
        $order = "desc";
        $obMess = new CEventMessage;
        $rsMess = $obMess::GetList($by, $order, $arFilter);
        while($arMess = $rsMess->GetNext())
        {
            $emailEventTemplateId = (int)$arMess['ID'];
            $obMess->Delete($emailEventTemplateId);
        }
    }
}