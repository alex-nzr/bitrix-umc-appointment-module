<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.02.2025
 * ==================================================
*/

namespace ANZ\Appointment\Integration\UmcSdk\Service\Exchange;

use ANZ\Appointment\Integration\UmcSdk\Model\BookAnAppointmentWithParams;
use ANZ\BitUmc\SDK\Core\Operation\Result;
use ANZ\BitUmc\SDK\Item\Order;

class Soap extends \ANZ\BitUmc\SDK\Service\Exchange\Soap
{
    public function sendOrder(Order $order): Result
    {
        return $this->getResponse(new BookAnAppointmentWithParams($order));
    }
}