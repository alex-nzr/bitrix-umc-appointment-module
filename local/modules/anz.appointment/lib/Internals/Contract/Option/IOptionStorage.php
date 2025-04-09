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
    const OPTION_TYPE_FILE_POSTFIX = '_FILE';

    public function getTabs(): array;
}