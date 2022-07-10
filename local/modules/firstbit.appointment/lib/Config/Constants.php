<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - Constants.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace FirstBit\Appointment\Config;

/**
 * Class Constants
 * @package FirstBit\Appointment\Config
 */
class Constants
{
    const APPOINTMENT_MODULE_ID      = 'firstbit.appointment';
    const APPOINTMENT_JS_EXTENSION   = "firstbit.dev.bx_popup";

    const CLINIC_ACTION_1C           = "GetListClinic";
    const EMPLOYEES_ACTION_1C        = "GetListEmployees";
    const SCHEDULE_ACTION_1C         = "GetSchedule";
    const NOMENCLATURE_ACTION_1C     = "GetNomenclatureAndPrices";
    const CREATE_ORDER_ACTION_1C     = "BookAnAppointmentWithParams";
    const DELETE_ORDER_ACTION_1C     = "CancelBookAnAppointment";
    const CREATE_WAIT_LIST_ACTION_1C = "FastBookAnAppointment";
    const CREATE_RESERVE_ACTION_1C   = "GetReserve";
    const GET_ORDER_STATUS_ACTION_1C = "GetAppointmentStatus";

    const DEMO_MODE = "N";
    const PATH_TO_DEMO_DATA_FILE = __DIR__."/../../store/demoData.json";

    const DEFAULT_SCHEDULE_PERIOD_DAYS = 14;
    const DEFAULT_APPOINTMENT_DURATION_SEC = 1800;

    const EMAIL_NOTE_EVENT_CODE    = "FIRSTBIT_APPOINTMENT_EMAIL_NOTE";
    const EMAIL_CONFIRM_EVENT_CODE = "FIRSTBIT_APPOINTMENT_EMAIL_CONFIRM";
    const SMS_CONFIRM_EVENT_CODE   = "FIRSTBIT_APPOINTMENT_SMS_CONFIRM";

    const CONFIRM_TYPE_PHONE = 'phone';
    const CONFIRM_TYPE_EMAIL = 'email';
    const CONFIRM_TYPE_NONE  = 'none';
}