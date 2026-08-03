<?php

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
