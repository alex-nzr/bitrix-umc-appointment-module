<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * Bit.Umc - Bitrix integration - RecordList.php
 * 14.02.2023 14:25
 * ==================================================
 */
namespace ANZ\Appointment\Component;

use ANZ\Appointment\Internals\Control\ServiceManager;
use ANZ\Appointment\Service\Container;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\UI\Filter\Options as FilterOptions;
use Bitrix\Main\UI\PageNavigation;
use CAjax;
use Exception;

/**
 * @class RecordList
 * @package ANZ\Appointment\Component
 */
class RecordList extends BaseComponent
{
    protected array           $allowedColumns;
    protected DataManager     $entity;
    protected PageNavigation  $pageNavObject;
    protected array           $rows;
    protected GridOptions     $gridOptions;
    protected string          $gridId;

    /**
     * @param $component
     * @throws \Exception
     */
    public function __construct($component = null)
    {
        parent::__construct($component);
        Loc::loadMessages(__FILE__);

        $this->gridId         = 'anz_appointment_admin_grid';
        $this->entity         = new (Container::getInstance()->getRecordDataClass());
        $this->allowedColumns = $this->getAllowedColumns();
        $this->gridOptions    = new GridOptions($this->gridId);
        $this->pageNavObject  = $this->setPageNavigation($this->gridId);
        $this->rows           = $this->setRows(
            $this->gridId, $this->pageNavObject->getOffset(), $this->pageNavObject->getLimit()
        );

        Extension::load(['ui.buttons', $this->moduleId.'.admin']);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    function checkRequirements(): bool
    {
        if ($this->App->GetGroupRight(ServiceManager::getModuleId()) < "R")
        {
            throw new Exception('Access to component denied');
        }
        return true;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        $navObject = $this->getPageNavigation();
        $columns = $this->getColumns();
        $rows = $this->getRows();
        $totalCount = $navObject->getRecordCount();

        return [
            'FILTER_PARAMS' => [
                'FILTER_ID' => $this->gridId,
                "GRID_ID"   => $this->gridId,
                'FILTER'    => $this->getFilterSettings(),
                'ENABLE_LIVE_SEARCH' => true,
                'ENABLE_LABEL' => true
            ],
            'GRID_PARAMS' => [
                'GRID_ID'       => $this->gridId,
                'NAV_OBJECT'    => $navObject,
                'COLUMNS'       => $columns,
                'ROWS'          => $rows,
                'AJAX_MODE'     => 'Y',
                'AJAX_ID'       => CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
                'PAGE_SIZES'    => [
                    ['NAME' => "5",   'VALUE' => '5'],
                    ['NAME' => '10',  'VALUE' => '10'],
                    ['NAME' => '20',  'VALUE' => '20'],
                    ['NAME' => '50',  'VALUE' => '50'],
                    ['NAME' => '100', 'VALUE' => '100']
                ],
                'TOTAL_ROWS_COUNT'          => $totalCount,
                'SHOW_CHECK_ALL_CHECKBOXES' => true,
                'SHOW_ROW_ACTIONS_MENU'     => true,
                'SHOW_ROW_CHECKBOXES'       => true,
                'SHOW_GRID_SETTINGS_MENU'   => true,
                'SHOW_NAVIGATION_PANEL'     => true,
                'SHOW_PAGINATION'           => true,
                'SHOW_SELECTED_COUNTER'     => true,
                'SHOW_TOTAL_COUNTER'        => true,
                'SHOW_PAGESIZE'             => true,
                'SHOW_ACTION_PANEL'         => true,
                'ACTION_PANEL'              => [],
                'ALLOW_COLUMNS_SORT'        => true,
                'ALLOW_COLUMNS_RESIZE'      => true,
                'ALLOW_HORIZONTAL_SCROLL'   => true,
                'ALLOW_SORT'                => true,
                'ALLOW_PIN_HEADER'          => true,
                'AJAX_OPTION_HISTORY'       => 'N',
                'AJAX_OPTION_JUMP'          => 'N',
            ]
        ];
    }

    /**
     * @param string $gridId
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    protected function setRows(string $gridId, int $offset, int $limit): array
    {
        $defaultSelect = array_keys($this->allowedColumns);
        $additionalSelect = ['USER_LOGIN' => 'USER.LOGIN'];
        $defaultSort = ['ID' => 'DESC'];
        $gridSort = $this->gridOptions->GetSorting(['sort' => $defaultSort]);
        $rows = [];
        $recordsRes = $this->entity::query()
            ->setOrder($gridSort['sort'])
            ->setSelect(array_merge($defaultSelect, $additionalSelect))
            ->setFilter($this->getFilterValues($gridId))
            ->setOffset($offset)
            ->setLimit($limit)
            ->countTotal(true)
            ->exec();

        $this->pageNavObject->setRecordCount($recordsRes->getCount());
        $records = $recordsRes->fetchAll();
        $deleteText = Loc::getMessage("ANZ_APPOINTMENT_BTN_DELETE_TEXT");
        foreach ($records as $item)
        {
            if ((int)$item['USER_ID'] > 0)
            {
                $item['USER_ID'] = $this->getUserProfileLink($item['USER_ID'], $item['USER_LOGIN']);
            }
            else
            {
                $item['USER_ID'] = 'Anonymous';
            }

            $rows[] = [
                'id' => $item['ID'],
                'data'    => array_intersect_key($item, $this->allowedColumns),
                'actions' => [
                    [
                        'text'    => $deleteText,
                        'onclick' => 'confirm("'.$deleteText.'?") 
                                ? BX.Anz.Appointment.Admin.deleteRecord('.$item["ID"].', "'.$gridId.'", "'.$item['XML_ID'].'") 
                                : void(0)'
                    ],
                    [
                        'text'    => Loc::getMessage("ANZ_APPOINTMENT_BTN_UPDATE_STATUS_TEXT"),
                        'onclick' => 'BX.Anz.Appointment.Admin.updateRecord('.$item["ID"].', "'.$gridId.'", "'.$item['XML_ID'].'")'
                    ],
                ],
            ];
        }

        return $rows;
    }

    /**
     * @param string $gridId
     * @return \Bitrix\Main\UI\PageNavigation
     */
    protected function setPageNavigation(string $gridId): PageNavigation
    {
        $nav = new PageNavigation($gridId);
        $nav_params = $this->gridOptions->GetNavParams();
        $nav->allowAllRecords(false)
            ->setPageSize(!empty($nav_params['nPageSize']) ? $nav_params['nPageSize'] : 20)
            ->initFromUri();

        return $nav;
    }

    /**
     * @param string $gridId
     * @return array
     */
    protected function getFilterValues(string $gridId): array
    {
        $filterOption = new FilterOptions($gridId);
        $filterData = $filterOption->getFilter();

        $arFilter = [];

        foreach ($filterData as $key => $val)
        {
            switch ($key)
            {
                case "DATE_CREATE_from":
                    $arFilter['>=DATE_CREATE'] = $val;
                    break;
                case "DATE_CREATE_to":
                    $arFilter['<=DATE_CREATE'] = $val;
                    break;
                case "DATETIME_VISIT_from":
                    $arFilter['>=DATETIME_VISIT'] = $val;
                    break;
                case "DATETIME_VISIT_to":
                    $arFilter['<=DATETIME_VISIT'] = $val;
                    break;
                case "ID_from":
                    $arFilter['>=ID'] = $val;
                    break;
                case "ID_to":
                    $arFilter['<=ID'] = $val;
                    break;
                case "DAYS_LEFT_from":
                    $arFilter['>=DAYS_LEFT'] = $val;
                    break;
                case "DAYS_LEFT_to":
                    $arFilter['<=DAYS_LEFT'] = $val;
                    break;
                case "USER_ID_from":
                    $arFilter['>=USER_ID'] = $val;
                    break;
                case "USER_ID_to":
                    $arFilter['<=USER_ID'] = $val;
                    break;
                case "FIND":
                    break;
                default:
                    if (isset($this->allowedColumns[$key])){
                        $arFilter[$key] = "%".$val."%";
                    }
                    break;
            }
        }

        return $arFilter;
    }

    /**
     * @return array[]
     */
    public function getFilterSettings(): array
    {
        $filterSettings = [];
        foreach ($this->allowedColumns as $id => $params) {
            $option = [
                'id' => $id,
                'name' => $params['name'],
                'type' => $params['type'],
            ];
            if ($params['type'] === 'dest_selector'){
                $option['params'] = [
                    'multiple' => 'N',
                    'context' => 'USER',
                    'contextCode' => 'U',
                    'enableAll' => 'N',
                    'enableUsers' => "Y",
                    'enableUserManager' => "Y",
                    'userSearchArea' => 'I',
                    "departmentSelectDisable" => "Y",
                    'enableDepartments' => 'N',
                    'departmentFlatEnable' => 'N',
                ];
            }
            $filterSettings[] = $option;
        }
        return $filterSettings;
    }

    /**
     * @return \Bitrix\Main\UI\PageNavigation
     */
    public function getPageNavigation(): PageNavigation
    {
        return $this->pageNavObject;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        $tableMap = $this->entity::getMap();
        $columns = [];
        foreach ($tableMap as $column){
            $name =  $column->getName();
            if (isset($this->allowedColumns[$name])){
                $columns[] = [
                    'id'      => $name,
                    'name'    => Loc::getMessage('ANZ_APPOINTMENT_TABLE_' . $name),
                    'default' => true,
                    'sort'    => $name === "COMMENT" ? false : $name,
                ];
            }
        }

        return $columns;
    }

    /**
     * @return array
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * @return array[]
     */
    protected function getAllowedColumns(): array
    {
        return [
            'ID'             => [
                'name' => Loc::getMessage("ANZ_APPOINTMENT_TABLE_ID"),
                'type' => 'number',
            ],
            'XML_ID'             => [
                'name' => Loc::getMessage("ANZ_APPOINTMENT_TABLE_XML_ID"),
                'type' => 'number',
            ],
            'DATE_CREATE'    => [
                'name' => Loc::getMessage("ANZ_APPOINTMENT_TABLE_DATE_CREATE"),
                'type' => 'date',
            ],
            'DATETIME_VISIT' => [
                'name' => Loc::getMessage("ANZ_APPOINTMENT_TABLE_DATETIME_VISIT"),
                'type' => 'date',
            ],
            'DAYS_LEFT'      => [
                'name' => Loc::getMessage("ANZ_APPOINTMENT_TABLE_DAYS_LEFT"),
                'type' => 'number',
            ],
            'CLINIC_TITLE'   => [
                'name' => Loc::getMessage("ANZ_APPOINTMENT_TABLE_CLINIC_TITLE"),
                'type' => 'string',
            ],
            'SPECIALTY'      => [
                'name' => Loc::getMessage("ANZ_APPOINTMENT_TABLE_SPECIALTY"),
                'type' => 'string',
            ],
            'DOCTOR_NAME'    => [
                'name' => Loc::getMessage("ANZ_APPOINTMENT_TABLE_DOCTOR_NAME"),
                'type' => 'string',
            ],
            'SERVICE_TITLE'  => [
                'name' => Loc::getMessage("ANZ_APPOINTMENT_TABLE_SERVICE_TITLE"),
                'type' => 'string',
            ],
            'PATIENT_NAME'   => [
                'name' => Loc::getMessage("ANZ_APPOINTMENT_TABLE_PATIENT_NAME"),
                'type' => 'string',
            ],
            'PATIENT_PHONE'  => [
                'name' => Loc::getMessage("ANZ_APPOINTMENT_TABLE_PATIENT_PHONE"),
                'type' => 'string',
            ],
            'PATIENT_EMAIL'  => [
                'name' => Loc::getMessage("ANZ_APPOINTMENT_TABLE_PATIENT_EMAIL"),
                'type' => 'string',
            ],
            'COMMENT'        => [
                'name' => Loc::getMessage("ANZ_APPOINTMENT_TABLE_COMMENT"),
                'type' => 'string',
            ],
            'STATUS_1C'      => [
                'name' => Loc::getMessage("ANZ_APPOINTMENT_TABLE_STATUS_1C"),
                'type' => 'string',
            ],
            'USER_ID'        => [
                'name' => Loc::getMessage("ANZ_APPOINTMENT_TABLE_USER_ID"),
                'type' => 'number',
            ],
        ];
    }

    /**
     * @param $userId
     * @param $userLogin
     * @return string
     */
    public function getUserProfileLink($userId, $userLogin): string
    {
        return "<a href='/bitrix/admin/user_edit.php?ID=".$userId."&lang=".LANGUAGE_ID."'>[" . $userId . "]".$userLogin."</a>";
    }

    /**
     * @param string $message
     * @param bool $isError
     * @return void
     */
    protected function showMessage(string $message, bool $isError = false): void
    {
        $isError ? ShowError($message) : ShowMessage($message);
    }
}