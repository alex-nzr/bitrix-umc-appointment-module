<?php
namespace FirstBit\Appointment\Services\Admin;


use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Exception;

class ListPageManager
{

    private DataManager $entityClass;

    /**
     * ListPageManager constructor.
     * @param string $entityClass
     */
    public function __construct(string $entityClass){
        $this->entityClass = new $entityClass;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        $tableMap = $this->entityClass::getMap();
        $columns = [];
        foreach ($tableMap as $column){
            $name =  $column->getName();
            $columns[] = [
                'id'      => $name,
                'name'    => $name,//Loc::getMessage('FIRSTBIT_APPOINTMENT_TABLE_' . $name)
                'default' => true,
                'sort'    => $name === "COMMENT" ? "N" : "Y",
            ];
        }

        return $columns;
    }

    /**
     * @return array
     */
    public function getRows(): array
    {
        try {
            $rows = [];
            $recordsRes = $this->entityClass::query()
                ->setOrder(['ID' => 'DESC'])
                ->setSelect(['ID', '*', 'UF_*'])
                ->setOffset(0)//$this->arResult["NAV"]->getOffset()
                ->setLimit(20) //$this->arResult["NAV"]->getLimit()
                ->countTotal(true)
                ->exec();

            //$this->arResult["NAV"]->setRecordCount($hlRes->getCount());
            //$this->arResult['TOTAL_ROWS_COUNT'] = $hlRes->getCount();
            foreach ($recordsRes as $item)
            {
                $rows[] = [
                    'id' => $item['ID'],
                    'data'    => $item,
                    'actions' => [
                        [
                            'text'    => 'Delete',//Loc::getMessage("BTN_OPEN_TEXT"),
                            'onclick' => 'window.alert("delete action")'
                        ],
                        [
                            'text'    => 'Update status',//Loc::getMessage("BTN_OPEN_TEXT"),
                            'onclick' => 'window.alert("Update status action")'
                        ],
                    ],
                ];
            }

            return $rows;
        }
        catch (Exception $e){
            return [];
        }
    }
}