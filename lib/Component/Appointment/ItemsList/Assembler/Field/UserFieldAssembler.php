<?php

namespace ANZ\Appointment\Component\Appointment\ItemsList\Assembler\Field;

class UserFieldAssembler extends \Bitrix\Main\Grid\Row\Assembler\Field\UserFieldAssembler
{
    protected function loadUserName(int $userId): string
    {
        $userName = parent::loadUserName($userId);
        $lang = LANGUAGE_ID;
        if (!empty($userName))
        {
            $userName = htmlspecialcharsbx($userName);
            return "<a href='/bitrix/admin/user_edit.php?ID=$userId&lang=$lang' target='_blank'>$userName</a>";
        }

        return (string)$userId;
    }
}
