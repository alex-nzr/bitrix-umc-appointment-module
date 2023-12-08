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

    const APPOINTMENT_MODULE_ID      = "anz.appointment";
    const APPOINTMENT_JS_EXTENSION   = "anz.appointment.bx_popup";

    const PATH_TO_DEMO_DATA_FILE = __DIR__."/../../store/demoData.json";

    const DEFAULT_SCHEDULE_PERIOD_DAYS     = 14;
    const DEFAULT_APPOINTMENT_DURATION_SEC = 1800;

    const EMAIL_NOTE_EVENT_CODE    = "ANZ_APPOINTMENT_EMAIL_NOTE";
    const EMAIL_CONFIRM_EVENT_CODE = "ANZ_APPOINTMENT_EMAIL_CONFIRM";
    const SMS_CONFIRM_EVENT_CODE   = "ANZ_APPOINTMENT_SMS_CONFIRM";

    const CONFIRM_TYPE_PHONE = 'phone';
    const CONFIRM_TYPE_EMAIL = 'email';
    const CONFIRM_TYPE_NONE  = 'none';


    /**Option constants*/
    const OPTION_TYPE_FILE_POSTFIX   = '_FILE';

    const OPTION_KEY_API_WS_URL      = 'appointment_api_ws_url';
    const OPTION_KEY_API_WS_LOGIN    = 'appointment_api_db_login';
    const OPTION_KEY_API_WS_PASSWORD = 'appointment_api_db_password';

    const OPTION_KEY_AUTO_INC               = 'appointment_settings_use_auto_injecting';
    const OPTION_KEY_SCHEDULE_DAYS          = 'appointment_api_schedule_days';
    const OPTION_KEY_DEFAULT_DURATION       = 'appointment_settings_default_duration';
    const OPTION_KEY_USE_NOMENCLATURE       = 'appointment_settings_use_nomenclature';
    const OPTION_KEY_DOCTOR_BEFORE_SERVICE  = 'appointment_settings_select_doctor_before_service';
    const OPTION_KEY_USE_TIME_STEPS         = 'appointment_settings_use_time_steps';
    const OPTION_KEY_TIME_STEP_DURATION     = 'appointment_settings_time_step_duration';
    const OPTION_KEY_STRICT_RELATIONS       = 'appointment_settings_strict_checking_relations';
    const OPTION_KEY_ALLOW_DOCTOR_WITHOUT_DPT = 'appointment_settings_show_doctors_without_dpt';
    const OPTION_KEY_USE_WAIT_LIST          = 'appointment_settings_use_waiting_list';
    const OPTION_KEY_EMAIL_NOTE             = 'appointment_settings_use_email_note';
    const OPTION_KEY_PRIVACY_PAGE           = 'appointment_settings_privacy_page_url';
    const OPTION_KEY_CONFIRM_MODE           = 'appointment_settings_confirm_with';

    const OPTION_KEY_LOGO            = 'appointment_view_logo_image'.self::OPTION_TYPE_FILE_POSTFIX;
    const OPTION_KEY_USE_CUSTOM_BTN  = 'appointment_view_use_custom_main_btn';
    const OPTION_KEY_CUSTOM_BTN_ID   = 'appointment_view_custom_main_btn_id';

    const OPTION_KEY_MAIN_BTN_BG        = '--appointment-start-btn-bg-color';
    const OPTION_KEY_MAIN_BTN_TEXT_CLR  = '--appointment-start-btn-text-color';
    const OPTION_KEY_FORM_BG            = '--appointment-main-color';
    const OPTION_KEY_FIELD_BG           = '--appointment-field-color';
    const OPTION_KEY_FORM_TEXT_CLR      = '--appointment-form-text-color';
    const OPTION_KEY_FORM_BTN_BG        = '--appointment-btn-bg-color';
    const OPTION_KEY_FORM_BTN_TEXT_CLR  = '--appointment-btn-text-color';

    const OPTION_KEY_USE_FTP_DATA       = 'appointment_settings_use_ftp_data';
    const OPTION_KEY_FTP_DATA_MAP       = 'appointment_settings_ftp_data_map';

    const OPTION_KEY_DEMO_MODE          = 'appointment_settings_use_demo_mode';

    const REQUEST_KEY_IGNORE_FTP        = 'IGNORE_FTP_DATA_OPTION';
}