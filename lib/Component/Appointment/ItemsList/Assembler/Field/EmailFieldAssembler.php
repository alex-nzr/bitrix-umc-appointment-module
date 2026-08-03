<?php
namespace ANZ\Appointment\Component\Appointment\ItemsList\Assembler\Field;

use Bitrix\Main\Grid\Row\FieldAssembler;

class EmailFieldAssembler extends FieldAssembler
{
    protected function prepareColumn($value): ?string
    {
        if (is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL))
        {
            return $this->getUserEmailLink($value);
        }

        return htmlspecialcharsbx($value);
    }

    protected function getUserEmailLink(string $email): string
    {
        $safeEmail = htmlspecialcharsbx($email);
        return '<a href="mailto:' . $safeEmail . '" target="_blank">' . $safeEmail . '</a>';
    }
}
