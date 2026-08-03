<?php
namespace ANZ\Appointment\Component;

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Errorable;
use CBitrixComponent;

abstract class BaseComponent extends CBitrixComponent implements Controllerable, Errorable
{
    use ComponentTrait;
}
