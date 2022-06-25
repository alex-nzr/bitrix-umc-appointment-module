<?php
namespace FirstBit\Appointment\Model;

class Record extends EO_Record
{
    /**
     * @param \string|\Bitrix\Main\DB\SqlExpression $status1c
     * @return $this
     */
    public function setStatus1c($status1c): Record
    {
        $this->set('STATUS_1C', $status1c);
        return $this;
    }
}