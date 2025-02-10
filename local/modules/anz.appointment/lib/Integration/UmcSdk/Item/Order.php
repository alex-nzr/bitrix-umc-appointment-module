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
    protected array $customParams = [];

    public function __construct(
        string $specialtyName,
        string $date,
        string $timeBegin,
        string $employeeUid,
        string $clinicUid,
        string $name,
        string $lastName,
        string $secondName,
        string $phone,
        string $email,
        string $address,
        string $comment,
        string $orderUid,
        string $clientBirthday,
        string $serviceDuration,
        array $services,
        array $customParams = []
    ){
        parent::__construct(
            $specialtyName, $date, $timeBegin, $employeeUid, $clinicUid,
            $name, $lastName, $secondName, $phone, $email, $address, $comment,
            $orderUid, $clientBirthday, $serviceDuration, $services
        );
        $this->customParams = $customParams;
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