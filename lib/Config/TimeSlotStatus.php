<?php
/*
 * ==================================================
 * This file is part of project bitrix.firstbit.umc
 * 23.08.2025
 * ==================================================
*/

namespace ANZ\Appointment\Config;

enum TimeSlotStatus: string
{
    case FREE = 'free';
    case BUSY = 'busy';
    case FREE_FORMATTED = 'freeFormatted';
}
