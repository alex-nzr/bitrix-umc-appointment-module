<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * "Bit.Umc - Bitrix integration" - BaseComponent.php
 * 07.02.2023 17:23
 * ==================================================
 */

namespace ANZ\Appointment\Component;

use ANZ\Appointment\Internals\Control\ServiceManager;
use CBitrixComponent;
use CMain;
use Exception;
use function ShowError;

/**
 * @class BaseComponent
 * @package CBit\GpnSm\ServiceDesk\Component
 */
abstract class BaseComponent extends CBitrixComponent
{
    public    string $moduleId;
    protected CMain  $App;
    protected bool   $excelMode;

    /**
     * @param $component
     */
    public function __construct($component = null)
    {
        parent::__construct($component);
        $this->App       = $GLOBALS['APPLICATION'];
        $this->moduleId  = ServiceManager::getModuleId();
        $this->excelMode = ($this->request->get('EXCEL_MODE') === 'Y');
    }

    /**
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams): array
    {
        return array_merge($arParams, [
            "CACHE_TYPE" => $arParams["CACHE_TYPE"] ?? "N",
            "CACHE_TIME" => $arParams["CACHE_TIME"] ?? 0,
        ]);
    }

    /**
     * @return void
     */
    final public function executeComponent(): void
    {
        try
        {
            if ($this->checkRequirements() && $this->startResultCache($this->arParams['CACHE_TIME']))
            {
                $this->arResult = $this->getResult();
                $this->includeComponentTemplate();
                $this->endResultCache();
            }
        }
        catch(Exception $e)
        {
            $this->AbortResultCache();
            $this->showMessage($e->getMessage(), true);
        }
    }

    /**
     * @param string $message
     * @param bool $isError
     */
    protected function showMessage(string $message, bool $isError = false): void
    {
        $isError ? ShowError($message) : ShowMessage($message);
    }

    /**
     * @return bool
     */
    abstract function checkRequirements(): bool;

    /**
     * @return array
     */
    abstract function getResult(): array;
}