<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 02.10.2025
 * ==================================================
*/

namespace ANZ\Appointment\Component\Appointment\ItemsList\Provider;

use ANZ\Appointment\Component\Appointment\ItemsList\Action\Row\DeleteAction;
use ANZ\Appointment\Component\Appointment\ItemsList\Action\Row\UpdateStatusAction;
use Bitrix\Main\Grid\Row\Action\DataProvider;

class RowActionsProvider extends DataProvider
{
    public function prepareActions(): array
    {
        return [
            new DeleteAction,
            new UpdateStatusAction,
        ];
    }
}