<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - EventType.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace FirstBit\Appointment\Event;

/**
 * Class EventType
 * @package FirstBit\Appointment\Event
 */
class EventType
{
    const ON_BEFORE_CLINICS_PARSED          = "onBeforeClinicsParsed";
    const ON_AFTER_CLINICS_PARSED           = "onAfterClinicsParsed";

    const ON_BEFORE_EMPLOYEES_PARSED        = "onBeforeEmployeesParsed";
    const ON_AFTER_EMPLOYEES_PARSED         = "onAfterEmployeesParsed";

    const ON_BEFORE_NOMENCLATURE_PARSED     = "onBeforeNomenclatureParsed";
    const ON_AFTER_NOMENCLATURE_PARSED      = "onAfterNomenclatureParsed";

    const ON_BEFORE_SCHEDULE_PARSED         = "onBeforeScheduleParsed";
    const ON_AFTER_SCHEDULE_PARSED          = "onAfterScheduleParsed";

    const ON_BEFORE_ORDER_SEND              = "onBeforeOrderSend";
}