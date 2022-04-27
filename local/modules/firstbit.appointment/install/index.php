<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\IO\Directory as Dir;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Internal\EventTypeTable;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\SiteTable;
use Bitrix\Main\Sms\TemplateTable;
use FirstBit\Appointment\Config\Constants;
use FirstBit\Appointment\Model\RecordTable;
use FirstBit\Appointment\Utils\Utils;

Loc::loadMessages(__FILE__);

class firstbit_appointment extends CModule
{
    private CMain $App;
    private ?string $docRoot;
    private string $partnerId;
    private string $moduleNameShort;

    public function __construct(){
        $this->App = $GLOBALS['APPLICATION'];
        $this->docRoot = Application::getDocumentRoot();
        $this->partnerId = 'firstbit';
        $this->moduleNameShort = 'appointment';

        $arModuleVersion = [];
        include(__DIR__."/version.php");

        $this->MODULE_ID            = $this->partnerId.".".$this->moduleNameShort;
        $this->MODULE_VERSION       = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE  = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME          = Loc::getMessage("FIRSTBIT_APPOINTMENT_MODULE_NAME");
        $this->MODULE_DESCRIPTION   = Loc::getMessage("FIRSTBIT_APPOINTMENT_MODULE_DESCRIPTION");
        $this->PARTNER_NAME         = Loc::getMessage("FIRSTBIT_APPOINTMENT_PARTNER_NAME");
        $this->PARTNER_URI          = Loc::getMessage("FIRSTBIT_APPOINTMENT_PARTNER_URI");
        $this->MODULE_SORT          = 100;
        $this->MODULE_GROUP_RIGHTS  = "Y";
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = "Y";
    }

    public function DoInstall(): void
    {
        try
        {
            $this->checkRequirements();

            ModuleManager::registerModule($this->MODULE_ID);

            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();
            $this->createMessageEvents();

            $this->App->IncludeAdminFile(
                Loc::getMessage("FIRSTBIT_APPOINTMENT_INSTALL_TITLE"),
                __DIR__."/step.php"
            );
        }
        catch (Exception $e)
        {
            $this->App->ThrowException($e->getMessage());
        }
    }

    public function DoUninstall(): void
    {
        try {
            $request = Context::getCurrent()->getRequest();

            if ($request->get('step') < 2)
            {
                $this->App->IncludeAdminFile(
                    Loc::getMessage("FIRSTBIT_APPOINTMENT_UNINSTALL_TITLE"),
                    __DIR__."/unStep_1.php"
                );
            }
            else
            {
                $this->deleteMessageEvents();
                $this->UnInstallFiles();
                $this->UnInstallEvents();
                if ($request->get('saveData') !== "Y"){
                    $this->UnInstallDB();
                }
                ModuleManager::unRegisterModule($this->MODULE_ID);

                $this->App->IncludeAdminFile(
                    Loc::getMessage("FIRSTBIT_APPOINTMENT_UNINSTALL_TITLE"),
                    __DIR__."/unStep_2.php"
                );
            }
        }
        catch (Exception $e)
        {
            $this->App->ThrowException($e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function InstallDB()
    {
        try {
            Loader::includeModule($this->MODULE_ID);

            $connection = Application::getConnection();
            $recordTableName = Base::getInstance(RecordTable::class)->getDBTableName();
            if(!$connection->isTableExists($recordTableName))
            {
                Base::getInstance(RecordTable::class)->createDbTable();
            }
        }
        catch (Exception $e){
            throw new Exception(Loc::getMessage("FIRSTBIT_APPOINTMENT_INSTALL_ERROR")." - ". $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function UnInstallDB(): void
    {
        try {
            Loader::includeModule($this->MODULE_ID);

            Option::delete($this->MODULE_ID);
            $connection = Application::getConnection();
            $recordTableName = Base::getInstance(RecordTable::class)->getDBTableName();
            if($connection->isTableExists($recordTableName))
            {
                $connection->dropTable($recordTableName);
            }
        }
        catch(Exception $e){
            throw new Exception(Loc::getMessage("FIRSTBIT_APPOINTMENT_UNINSTALL_ERROR")." - ". $e->getMessage());
        }
    }

    public function InstallEvents()
    {

    }

    public function UnInstallEvents()
    {

    }

    public function InstallFiles()
    {
        CopyDirFiles(__DIR__.'/js/', $this->docRoot.'/bitrix/js/'.$this->partnerId."/".$this->moduleNameShort, true, true);
        CopyDirFiles(__DIR__.'/css/', $this->docRoot.'/bitrix/css/'.$this->partnerId."/".$this->moduleNameShort, true, true);
        CopyDirFiles(__DIR__.'/admin/', $this->docRoot.'/bitrix/admin', true);
        CopyDirFiles(__DIR__.'/components/', $this->docRoot.'/bitrix/components', true, true);
    }

    public function UnInstallFiles()
    {
        DeleteDirFiles(__DIR__.'/admin/', $this->docRoot.'/bitrix/admin');

        if (Dir::isDirectoryExists($this->docRoot . '/bitrix/components/'.$this->partnerId.'/')){
            Dir::deleteDirectory($this->docRoot . '/bitrix/components/'.$this->partnerId.'/');
        }
        if (Dir::isDirectoryExists($this->docRoot . '/bitrix/css/'.$this->partnerId."/".$this->moduleNameShort.'/')){
            Dir::deleteDirectory($this->docRoot . '/bitrix/css/'.$this->partnerId. "/".$this->moduleNameShort.'/');
        }
        if (Dir::isDirectoryExists($this->docRoot . '/bitrix/js/'.$this->partnerId."/".$this->moduleNameShort.'/')){
            Dir::deleteDirectory($this->docRoot . '/bitrix/js/'.$this->partnerId."/".$this->moduleNameShort.'/');
        }
    }

    public function createMessageEvents(): void
    {
        try {
            Loader::includeModule($this->MODULE_ID);

            $emailNoteEventTypeID = $this->createEmailNoteEvent();
            if (!($emailNoteEventTypeID > 0)){
                throw new Exception($this->App->LAST_ERROR);
            }

            $emailConfirmEventTypeID = $this->createEmailConfirmEvent();
            if (!($emailConfirmEventTypeID > 0)){
                throw new Exception($this->App->LAST_ERROR);
            }

            $smsEventTypeID = $this->createSmsConfirmEvent();
            if (!($smsEventTypeID > 0)){
                throw new Exception($this->App->LAST_ERROR);
            }

            $siteIds = Utils::getAllSiteIds();
            $this->createMessageEventTemplates($siteIds);
        }
        catch (Exception $e){
            $this->App->ThrowException(
                Loc::getMessage("FIRSTBIT_APPOINTMENT_INSTALL_ERROR")." - ". $e->getMessage()
            );
        }
    }

    public function createEmailNoteEvent(): int
    {
        $obEventType = new CEventType;
        $id = $obEventType->Add([
            "EVENT_TYPE"    => EventTypeTable::TYPE_EMAIL,
            "EVENT_NAME"    => Constants::EMAIL_NOTE_EVENT_CODE,
            "NAME"          => Loc::getMessage("FIRSTBIT_APPOINTMENT_EMAIL_NOTE_NAME"),
            "LID"           => 'ru',
            "DESCRIPTION"   =>  "#CODE# - " . Loc::getMessage("FIRSTBIT_APPOINTMENT_NOTE_DESC_TEXT") . "\n" .
                                "#EMAIL_TO# - " . Loc::getMessage("FIRSTBIT_APPOINTMENT_NOTE_DESC_EMAIL_TO")
        ]);

        return (int)$id;
    }

    public function createEmailConfirmEvent(): int
    {
        $obEventType = new CEventType;
        $id = $obEventType->Add([
            "EVENT_TYPE"    => EventTypeTable::TYPE_EMAIL,
            "EVENT_NAME"    => Constants::EMAIL_CONFIRM_EVENT_CODE,
            "NAME"          => Loc::getMessage("FIRSTBIT_APPOINTMENT_EMAIL_CONFIRM_NAME"),
            "LID"           => 'ru',
            "DESCRIPTION"   => "#CODE# - " . Loc::getMessage("FIRSTBIT_APPOINTMENT_CONFIRM_DESC_CODE")
        ]);

        return (int)$id;
    }

    public function createSmsConfirmEvent(): int
    {
        $obEventType = new CEventType;
        $id = $obEventType->Add([
            "EVENT_TYPE"    => EventTypeTable::TYPE_SMS,
            "EVENT_NAME"    => Constants::SMS_CONFIRM_EVENT_CODE,
            "NAME"          => Loc::getMessage("FIRSTBIT_APPOINTMENT_SMS_CONFIRM_NAME"),
            "LID"           => 'ru',
            "DESCRIPTION"   => "#CODE# - " . Loc::getMessage("FIRSTBIT_APPOINTMENT_CONFIRM_DESC_CODE")
        ]);

        return (int)$id;
    }

    public function deleteMessageEvents(): void
    {
        try {
            Loader::includeModule($this->MODULE_ID);
            $obEventType = new CEventType;
            $obEventType->Delete(Constants::EMAIL_NOTE_EVENT_CODE);
            $obEventType->Delete(Constants::EMAIL_CONFIRM_EVENT_CODE);
            $obEventType->Delete(Constants::SMS_CONFIRM_EVENT_CODE);
            $this->deleteMessageEventTemplates();
        }
        catch (Exception $e){
            $this->App->ThrowException(
                Loc::getMessage("FIRSTBIT_APPOINTMENT_UNINSTALL_ERROR")." - ". $e->getMessage()
            );
        }
    }

    /**
     * @param array $siteIds
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public function createMessageEventTemplates(array $siteIds): void
    {
        $this->createEmailNoteTemplate($siteIds);
        $this->createEmailConfirmTemplate($siteIds);
        $this->createSmsTemplate($siteIds);
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
            "SUBJECT"    => Loc::getMessage("FIRSTBIT_APPOINTMENT_EMAIL_NOTE_NAME"),
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
            "SUBJECT"    => Loc::getMessage("FIRSTBIT_APPOINTMENT_CONFIRM_DESC_CODE"),
            "BODY_TYPE"  => "text",
            "MESSAGE"    => Loc::getMessage("FIRSTBIT_APPOINTMENT_CONFIRM_DESC_CODE") . " - #CODE#",
        ];
        $obTemplate = new CEventMessage;
        $id = $obTemplate->Add($params);
        return (int)$id;
    }

    /**
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Exception
     */
    public function createSmsTemplate(array $siteIds): int
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
            throw new Exception(json_encode($result->getErrorMessages()));
        }
    }

    /**
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Exception
     */
    public function deleteMessageEventTemplates(): void
    {
        $this->deleteEmailTemplates();
        $this->deleteSmsTemplates();
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

    /**
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Exception
     */
    public function deleteSmsTemplates(): void
    {
        $res = TemplateTable::query()
            ->setSelect(['ID'])
            ->setFilter(["EVENT_NAME"    => Constants::SMS_CONFIRM_EVENT_CODE])
            ->exec()
            ->fetchAll();
        if (is_array($res) && count($res) > 0)
        {
            foreach ($res as $item) {
                TemplateTable::delete($item['ID']);
            }
        }
    }

    public function GetModuleRightList(): array
    {
        return [
            "reference_id" => array("D","R","W"),
            "reference" => array(
                "[D] ".Loc::getMessage("FIRSTBIT_APPOINTMENT_DENIED"),
                "[R] ".Loc::getMessage("FIRSTBIT_APPOINTMENT_READ_COMPONENT"),
                "[W] ".Loc::getMessage("FIRSTBIT_APPOINTMENT_FULL"))
        ];
    }

    /**
     * @throws \Exception
     */
    protected function checkRequirements(): void
    {
        $requirePhp = '7.4.0';

        if (!CheckVersion(PHP_VERSION, $requirePhp))
        {
            throw new Exception(Loc::getMessage(
                'FIRSTBIT_APPOINTMENT_INSTALL_REQUIRE_PHP',
                [ '#VERSION#' => $requirePhp ]
            ));
        }

        $requireModules = [
            'main'  => '21.0.0',
        ];

        foreach ($requireModules as $moduleName => $moduleVersion)
        {
            $currentVersion = ModuleManager::getVersion($moduleName);

            if (!CheckVersion($currentVersion, $moduleVersion))
            {
                throw new Exception(Loc::getMessage('FIRSTBIT_APPOINTMENT_INSTALL_ERROR_VERSION', [
                    '#MODULE#' => $moduleName,
                    '#VERSION#' => $moduleVersion
                ]));
            }
        }
    }
}