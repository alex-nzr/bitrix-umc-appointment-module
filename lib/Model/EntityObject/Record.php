<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Model\EntityObject;

use Bitrix\Main\ORM\Objectify\EntityObject;

class Record extends EntityObject
{
    /**
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public function setStatus1c($status1c): Record
    {
        $this->set('STATUS_1C', $status1c);
        return $this;
    }
}