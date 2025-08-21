<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 04.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Internals\Contract\Option;

interface IOptionStorage
{
    public function getTabs(): array;
}