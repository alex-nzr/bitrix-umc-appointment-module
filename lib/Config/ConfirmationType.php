<?php
namespace ANZ\Appointment\Config;

enum ConfirmationType: string
{
    case PHONE = 'phone';
    case EMAIL = 'email';
    case NONE  = 'none';
}
