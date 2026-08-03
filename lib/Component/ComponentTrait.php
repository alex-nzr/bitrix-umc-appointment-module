<?php
namespace ANZ\Appointment\Component;

use ANZ\Appointment\Config\Configuration;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use CMain;
use Throwable;

trait ComponentTrait
{
    public string $moduleId;
    protected CMain $App;
    protected bool $excelMode;
    protected ErrorCollection $errorCollection;

    /**
     * @throws \Exception
     */
    public function __construct($component = null)
    {
        parent::__construct($component);
        $this->App       = $GLOBALS['APPLICATION'];
        $this->moduleId  = Configuration::getModuleId();
        $this->excelMode = ($this->request->get('EXCEL_MODE') === 'Y');
        $this->errorCollection = new ErrorCollection();
    }

    public function onPrepareComponentParams($arParams): array
    {
        return array_merge($arParams, [
            "CACHE_TYPE" => $arParams["CACHE_TYPE"] ?? "A",
            "CACHE_TIME" => $arParams["CACHE_TIME"] ?? 0,
        ]);
    }

    public function showMessage(string $message, bool $isError = false): void
    {
        $isError ? ShowError($message) : ShowMessage($message);
    }

    public function executeComponent(): void
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
        catch(Throwable $e)
        {
            $this->AbortResultCache();
            $this->showMessage($e->getMessage(), true);
        }
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

    abstract public function checkRequirements(): bool;

    abstract public function getResult(): array;
}
