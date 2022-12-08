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
namespace ANZ\Appointment\Config;

/**
 * Class Constants
 * @package ANZ\Appointment\Config
 */
class Constants
{
    const PATH_TO_LOGFILE            = __DIR__.'/../../log.txt';

    const APPOINTMENT_MODULE_ID      = 'anz.appointment';
    const APPOINTMENT_JS_EXTENSION   = "anz.appointment.bx_popup";

    const CLINIC_ACTION_1C           = "GetListClinic";
    const EMPLOYEES_ACTION_1C        = "GetListEmployees";
    const SCHEDULE_ACTION_1C         = "GetSchedule20";
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

    const EMAIL_NOTE_EVENT_CODE    = "ANZ_APPOINTMENT_EMAIL_NOTE";
    const EMAIL_CONFIRM_EVENT_CODE = "ANZ_APPOINTMENT_EMAIL_CONFIRM";
    const SMS_CONFIRM_EVENT_CODE   = "ANZ_APPOINTMENT_SMS_CONFIRM";

    const CONFIRM_TYPE_PHONE = 'phone';
    const CONFIRM_TYPE_EMAIL = 'email';
    const CONFIRM_TYPE_NONE  = 'none';
}