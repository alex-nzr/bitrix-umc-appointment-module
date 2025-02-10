<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.02.2025
 * ==================================================
*/
namespace ANZ\Appointment\Integration\UmcSdk\Model;

use ANZ\Appointment\Integration\UmcSdk\Item\Order;
use ANZ\BitUmc\SDK\Core\Model\Request\Parameter;

class BookAnAppointmentWithParams extends \ANZ\BitUmc\SDK\Core\Model\Request\Soap\BookAnAppointmentWithParams
{
    public function __construct(Order $appointment)
    {
        parent::__construct($appointment);
        if (!empty($appointment->getCustomParams()))
        {
            foreach ($appointment->getCustomParams() as $paramName => $paramValue)
            {
                $this->Params[] = new Parameter($paramName, $paramValue);
            }
        }
    }
}