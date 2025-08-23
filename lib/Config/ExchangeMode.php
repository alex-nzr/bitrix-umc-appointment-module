<?php
/*
 * ==================================================
 * This file is part of project bitrix.firstbit.umc
 * 22.08.2025
 * ==================================================
*/
namespace ANZ\Appointment\Config;

enum ExchangeMode: string
{
    case HTTP = 'Http';
    case SOAP = 'SOAP';
}
