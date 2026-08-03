<?php
namespace ANZ\Appointment\Config;

enum ExchangeMode: string
{
    case HTTP = 'Http';
    case SOAP = 'SOAP';
}
