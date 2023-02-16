<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - Record.php
 * 10.07.2022 22:37
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