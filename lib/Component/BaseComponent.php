<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 07.02.2023
 * ==================================================
*/
namespace ANZ\Appointment\Component;

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Errorable;
use CBitrixComponent;

abstract class BaseComponent extends CBitrixComponent implements Controllerable, Errorable
{
    use ComponentTrait;
}