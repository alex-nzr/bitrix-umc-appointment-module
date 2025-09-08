<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 08.09.2025
 * ==================================================
*/

namespace ANZ\Appointment\Integration\UmcSdk\Mapper;

use ANZ\BitUmc\SDK\Builder\Order as OrderBuilder;
use ANZ\BitUmc\SDK\Item\Order as OrderItem;
use DateTime;

class SdkRequestFromParams
{
    /**
     * @throws \Exception
     */
    public function bookingItemFromParams(string $clinicUid, string $employeeUid, DateTime $dateTimeBegin, int $serviceDuration): OrderItem
    {
        return OrderBuilder::createReserve()
            ->setClinicUid($clinicUid)
            ->setEmployeeUid($employeeUid)
            ->setDateTimeBegin($dateTimeBegin)
            ->setAppointmentDuration($serviceDuration)
            ->build();
    }
}