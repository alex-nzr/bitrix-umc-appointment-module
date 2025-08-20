<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.02.2025
 * ==================================================
*/

namespace ANZ\Appointment\Integration\UmcSdk\Builder;

use ANZ\Appointment\Integration\UmcSdk\Item\Order as OrderItem;

class Order extends \ANZ\BitUmc\SDK\Builder\Order
{
    protected array $customParams = [];

    public function setCustomParams(array $customParams = []): static
    {
        $this->customParams = $customParams;
        return $this;
    }

    public function build(): OrderItem
    {
        $this->checkRequiredParams();
        $this->checkAndFillNotRequiredParams();

        return new OrderItem(
            $this->specialtyName,
            $this->date,
            $this->timeBegin,
            $this->employeeUid,
            $this->clinicUid,
            $this->name,
            $this->lastName,
            $this->secondName,
            $this->phone,
            $this->email,
            $this->address,
            $this->comment,
            $this->orderUid,
            $this->clientBirthday,
            $this->serviceDuration,
            $this->services,
            $this->customParams
        );
    }
}