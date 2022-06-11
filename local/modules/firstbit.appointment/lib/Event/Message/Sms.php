<?php


namespace FirstBit\Appointment\Event\Message;


use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Internal\EventTypeTable;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\SiteTable;
use Bitrix\Main\Sms\TemplateTable;
use CEventType;
use Exception;
use FirstBit\Appointment\Config\Constants;

class Sms
{
    /**
     * @throws \Exception
     */
    public function createSmsConfirmEvent(): int
    {
        $arFields = [
            "EVENT_TYPE"    => EventTypeTable::TYPE_SMS,
            "EVENT_NAME"    => Constants::SMS_CONFIRM_EVENT_CODE,
            "NAME"          => Loc::getMessage("FIRSTBIT_APPOINTMENT_SMS_CONFIRM_NAME"),
            "LID"           => 'ru',
            "DESCRIPTION"   => "#CODE# - " . Loc::getMessage("FIRSTBIT_APPOINTMENT_CONFIRM_DESC_CODE")
        ];
        $result = EventTypeTable::add($arFields);
        return $result->getId();
    }

    /**
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Exception
     */
    public function createSmsConfirmTemplate(array $siteIds): int
    {
        $params = [
            "EVENT_NAME"    => Constants::SMS_CONFIRM_EVENT_CODE,
            "ACTIVE"        => "Y",
            "SENDER"        => '#DEFAULT_SENDER#',
            "RECEIVER"      => '#USER_PHONE#',
            "MESSAGE"       => Loc::getMessage("FIRSTBIT_APPOINTMENT_CONFIRM_DESC_CODE") . " - #CODE#",
            "LANGUAGE_ID"   => "ru"
        ];

        $entity = TemplateTable::getEntity();
        $template = $entity->createObject();
        $fields = $template->entity->getFields();

        foreach($params as $fieldName => $value)
        {
            if($fields[$fieldName] instanceof BooleanField)
            {
                $value = ($value === "Y");
            }
            $template->set($fieldName, $value);
        }

        foreach($siteIds as $lid)
        {
            $site = SiteTable::getEntity()->wakeUpObject($lid);
            $template->addToSites($site);
        }

        $result = $template->save();

        if($result->isSuccess())
        {
            return (int)$result->getId();
        }
        else
        {
            throw new Exception(implode("; ", $result->getErrorMessages()));
        }
    }

    public function deleteSmsEvents()
    {
        $obEventType = new CEventType;
        $obEventType->Delete(Constants::SMS_CONFIRM_EVENT_CODE);
    }

    /**
     * @throws \Exception
     */
    public function deleteSmsTemplates(): void
    {
        $res = TemplateTable::query()
            ->setSelect(['ID'])
            ->setFilter(["EVENT_NAME" => Constants::SMS_CONFIRM_EVENT_CODE])
            ->fetchAll();
        if (is_array($res) && count($res) > 0)
        {
            foreach ($res as $item) {
                TemplateTable::delete($item['ID']);
            }
        }
    }
}