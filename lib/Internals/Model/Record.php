<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Internals\Model;

/**
 * @class Record
 * @package ANZ\Appointment\Internals\Model
 */
class Record extends EO_Record
{
    /**
     * @param $status1c
     * @return $this
     */
    public function setStatus1c($status1c): Record
    {
        $this->set('STATUS_1C', $status1c);
        return $this;
    }
}