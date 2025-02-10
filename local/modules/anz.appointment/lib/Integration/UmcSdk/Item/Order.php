<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.02.2025
 * ==================================================
*/

namespace ANZ\Appointment\Integration\UmcSdk\Item;

class Order extends \ANZ\BitUmc\SDK\Item\Order
{
    public function __construct(
        protected readonly string $specialtyName,
        protected readonly string $date,
        protected readonly string $timeBegin,
        protected readonly string $employeeUid,
        protected readonly string $clinicUid,
        protected readonly string $name,
        protected readonly string $lastName,
        protected readonly string $secondName,
        protected readonly string $phone,
        protected readonly string $email,
        protected readonly string $address,
        protected readonly string $comment,
        protected readonly string $orderUid,
        protected readonly string $clientBirthday,
        protected readonly string $serviceDuration,
        protected readonly array $services,
        protected array $customParams = []
    ){
        parent::__construct(
            $specialtyName, $date, $timeBegin, $employeeUid, $clinicUid,
            $name, $lastName, $secondName, $phone, $email, $address, $comment,
            $orderUid, $clientBirthday, $serviceDuration, $services
        );
    }

    public function setCustomParams(array $customParams): static
    {
        $this->customParams = $customParams;
        return $this;
    }

    public function getCustomParams(): array
    {
        return $this->customParams;
    }
}