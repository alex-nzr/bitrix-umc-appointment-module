<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 07.02.2023
 * ==================================================
*/
namespace ANZ\Appointment\Component;

use ANZ\Appointment\Internals\ServiceManager;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use CBitrixComponent;
use CMain;
use Exception;
use function ShowError;

abstract class BaseComponent extends CBitrixComponent implements Controllerable, Errorable
{
    public string $moduleId;
    protected CMain $App;
    protected bool $excelMode;
    protected ErrorCollection $errorCollection;

    public function __construct($component = null)
    {
        parent::__construct($component);
        $this->App       = $GLOBALS['APPLICATION'];
        $this->moduleId  = ServiceManager::getModuleId();
        $this->excelMode = ($this->request->get('EXCEL_MODE') === 'Y');
        $this->errorCollection = new ErrorCollection();
    }

    public function onPrepareComponentParams($arParams): array
    {
        return array_merge($arParams, [
            "CACHE_TYPE" => $arParams["CACHE_TYPE"] ?? "A",
            "CACHE_TIME" => $arParams["CACHE_TIME"] ?? 3600,
        ]);
    }

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

    public function showMessage(string $message, bool $isError = false): void
    {
        $isError ? ShowError($message) : ShowMessage($message);
    }

    /**
     * @return Error[]
     */
    public function getErrors(): array
    {
        return $this->errorCollection->toArray();
    }

    public function getErrorByCode($code): ?Error
    {
        return $this->errorCollection->getErrorByCode($code);
    }

    public function configureActions(): array
    {
        return [];
    }

    protected function addError(Error $error): static
    {
        $this->errorCollection[] = $error;
        return $this;
    }

    abstract function checkRequirements(): bool;

    abstract function getResult(): array;
}