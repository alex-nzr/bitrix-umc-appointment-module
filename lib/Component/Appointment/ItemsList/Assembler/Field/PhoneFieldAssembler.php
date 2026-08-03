<?php

namespace ANZ\Appointment\Component\Appointment\ItemsList\Assembler\Field;

use Bitrix\Main\Grid\Row\FieldAssembler;

class PhoneFieldAssembler extends FieldAssembler
{
    protected function prepareColumn($value): ?string
    {
        if (is_string($value) && str_starts_with($value, '+7') && strlen($value) === 12)
        {
            $safeValue = htmlspecialcharsbx($value);
            return '<a href="tel:' . $safeValue . '" target="_blank">' . $safeValue . '</a>';
        }

        return htmlspecialcharsbx($value);
    }
}
