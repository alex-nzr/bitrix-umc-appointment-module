<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Config;

class Constants
{
    const EMAIL_NOTE_EVENT_CODE    = "ANZ_APPOINTMENT_EMAIL_NOTE";
    const EMAIL_CONFIRM_EVENT_CODE = "ANZ_APPOINTMENT_EMAIL_CONFIRM";
    const SMS_CONFIRM_EVENT_CODE   = "ANZ_APPOINTMENT_SMS_CONFIRM";

    const CONFIRM_TYPE_PHONE = 'phone';
    const CONFIRM_TYPE_EMAIL = 'email';
    const CONFIRM_TYPE_NONE  = 'none';

    const JS_EXTENSION_BX_POPUP = 'anz.appointment.bx_popup';
    const JS_EXTENSION_FORM_VUE = 'anz.appointment.form-vue';

    /**Option constants*/
    const OPTION_KEY_PRIVACY_PAGE = 'appointment_settings_privacy_page_url';
    const OPTION_KEY_AUTO_INC = 'appointment_settings_use_auto_injecting';
    const OPTION_KEY_DEMO_MODE = 'appointment_settings_use_demo_mode';

    const OPTION_KEY_API_WS_URL      = 'appointment_api_ws_url';
    const OPTION_KEY_API_WS_LOGIN    = 'appointment_api_ws_login';
    const OPTION_KEY_API_WS_PASSWORD = 'appointment_api_ws_password';
    const OPTION_KEY_API_HS_TOKEN = 'appointment_api_hs_token';
    const OPTION_KEY_EXCHANGE_MODE = 'appointment_exchange_mode';
    const OPTION_KEY_EXCHANGE_USE_SERVICES   = 'appointment_exchange_use_services';
    const OPTION_KEY_EXCHANGE_AGENT_ACTIVE   = 'appointment_exchange_agent_active';
    const OPTION_KEY_EXCHANGE_NEXT_EXEC_DATE = 'appointment_exchange_next_exec_date';
    const OPTION_KEY_EXCHANGE_LAST_EXEC_DATE = 'appointment_exchange_last_exec_date';
    const OPTION_KEY_EXCHANGE_EXEC_INTERVAL  = 'appointment_exchange_exec_interval';
    const OPTION_KEY_EXCHANGE_SCHEDULE_PERIOD = 'appointment_exchange_schedule_period';
    const OPTION_KEY_EXCHANGE_DEFAULT_APPOINTMENT_DURATION = 'appointment_exchange_default_app_duration';
    const OPTION_KEY_EXCHANGE_CACHE_TTL = 'appointment_exchange_cache_ttl';

    const OPTION_KEY_USE_WAIT_LIST          = 'appointment_settings_use_waiting_list';
    const OPTION_KEY_EMAIL_NOTE             = 'appointment_settings_use_email_note';
    const OPTION_KEY_EXCHANGE_CONFIRM_MODE = 'appointment_exchange_confirm_mode';
    const OPTION_KEY_USE_TIME_STEPS         = 'appointment_settings_use_time_steps';
    const OPTION_KEY_TIME_STEP_DURATION     = 'appointment_settings_time_step_duration';
    const OPTION_KEY_STRICT_RELATIONS       = 'appointment_settings_strict_checking_relations';
    const OPTION_KEY_ALLOW_DOCTOR_WITHOUT_DPT = 'appointment_settings_show_doctors_without_dpt';

    const OPTION_KEY_JS_EXTENSION    = 'appointment_view_js_ext';
    const OPTION_KEY_CUSTOM_JS_EXTENSION = 'appointment_view_custom_js_ext';
    const OPTION_KEY_LOGO            = 'appointment_view_logo_image';
    const OPTION_KEY_USE_CUSTOM_BTN  = 'appointment_view_use_custom_main_btn';
    const OPTION_KEY_CUSTOM_BTN_ID   = 'appointment_view_custom_main_btn_id';
    const OPTION_KEY_MAIN_BTN_BG        = '--appointment-start-btn-bg-color';
    const OPTION_KEY_MAIN_BTN_TEXT_CLR  = '--appointment-start-btn-text-color';
    const OPTION_KEY_TEMPLATE_MAIN_COLOR = '--appointment-main-color';
    const OPTION_KEY_FIELD_BG           = '--appointment-field-color';
    const OPTION_KEY_FORM_TEXT_CLR      = '--appointment-form-text-color';
    const OPTION_KEY_FORM_BTN_BG        = '--appointment-btn-bg-color';
    const OPTION_KEY_FORM_BTN_TEXT_CLR  = '--appointment-btn-text-color';

    const OPTION_KEY_DEBUG_LOGS_TTL = 'appointment_debug_logs_ttl';
    const PASSWORD_MASKED_VALUE = '***********';
}