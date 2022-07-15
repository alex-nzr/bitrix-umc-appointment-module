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
namespace ANZ\Appointment\Model;

/**
 * Class Record
 * @package ANZ\Appointment\Model
 */
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