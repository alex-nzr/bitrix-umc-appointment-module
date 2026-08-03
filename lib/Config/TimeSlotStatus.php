<?php

namespace ANZ\Appointment\Config;

enum TimeSlotStatus: string
{
    case FREE = 'free';
    case BUSY = 'busy';
    case FREE_FORMATTED = 'freeFormatted';
}
