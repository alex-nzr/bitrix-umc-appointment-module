<?php
$hintTemplate = '<span class="option-hint-container"><span>%s</span><span data-hint="%s" data-hint-html></span></span>';
//sprintf($hintTemplate , 'option title', 'hint text');

//Common
$MESS['ANZ_APPOINTMENT_MODULE_NOT_LOADED'] = "Не удалось подключить модуль записи на приём";
$MESS["ANZ_APPOINTMENT_ACCESS_DENIED"] = "Доступ к модулю запрещён";

//Admin pages
$MESS['ANZ_ADMIN_LIST_PAGE_TITLE'] = "Список записей на приём";
$MESS['ANZ_ADMIN_SETTINGS_PAGE_TITLE'] = "Настройки модуля";

//Admin menu
$MESS['ANZ_APPOINTMENT_MENU_GLOBAL_TITLE'] = 'ANZ';
$MESS['ANZ_APPOINTMENT_MENU_MAIN_TITLE'] = 'ANZ. Запись на приём в БИТ УМЦ';
$MESS['ANZ_APPOINTMENT_MENU_LIST_TITLE'] = 'Список записей';
$MESS['ANZ_APPOINTMENT_MENU_SETTINGS_TITLE'] = 'Настройки модуля';

//Module Options
$MESS['ANZ_APPOINTMENT_MODULE_SETTINGS'] = "Настройки модуля";
$MESS['ANZ_APPOINTMENT_PRIVACY_PAGE_URL'] = sprintf($hintTemplate,'Ссылка на политику конфиденциальности сайта', 'Необходима для размещения на форме записи');
$MESS['ANZ_APPOINTMENT_USE_AUTO_INJECTING_ON'] = sprintf($hintTemplate, 'Автоматическое подключение', 'При включенной опции кнопка онлайн-записи появится на всех страницах сайта автоматически, если не указан id собственной кнопки.<br>При отключенной опции, нужно самостоятельно разместить на сайте компонент записи.');
$MESS['ANZ_APPOINTMENT_USE_DEMO_MODE_ON'] = sprintf($hintTemplate , 'Включить демо-режим', 'При включённой опции данные будут загружаться из файла в директории модуля, а отправка заявки будет всегда успешна');

/*Appointment options*/
$MESS['ANZ_APPOINTMENT_APP_SETTINGS'] = 'Настройки записи на приём';
$MESS['ANZ_APPOINTMENT_USE_EMAIL_NOTE'] = 'Отправлять информацию о записи на email';
$MESS['ANZ_APPOINTMENT_USE_WAITING_LIST'] = sprintf($hintTemplate , "Запись в лист ожидания", 'При включенной опции запись в 1С будет создаваться не как «Заявка», а как «Лист ожидания»');
$MESS['ANZ_APPOINTMENT_EXCHANGE_CONFIRM_MODE'] = sprintf($hintTemplate , 'Включить подтверждение записи', 'Для смс-подтверждения необходимо выбрать и настроить провайдера SMS в настройках модуля «Служба сообщений». Затем установить отправителя по умолчанию в настройках главного модуля.');
$MESS['ANZ_APPOINTMENT_EXCHANGE_CONFIRM_NONE'] = "Выключено";
$MESS['ANZ_APPOINTMENT_EXCHANGE_CONFIRM_PHONE'] = "По СМС";
$MESS['ANZ_APPOINTMENT_EXCHANGE_CONFIRM_EMAIL'] = "По email";
$MESS['ANZ_APPOINTMENT_EXCHANGE_DEFAULT_APPOINTMENT_DURATION'] = sprintf($hintTemplate , 'Длительность приёма по умолчанию, если не указана в 1С (в секундах)', 'Применяется если не указана длительность приема у врача и услуги');

$MESS['ANZ_APPOINTMENT_OTHER_SETTINGS'] = 'Устаревшие настройки. Актуальны для js-расширения "Первый вариант"';
$MESS['ANZ_APPOINTMENT_SELECT_DOCTOR_BEFORE_SERVICE'] = sprintf($hintTemplate , 'Выбирать сначала доктора, потом услугу', 'При включенной опции, пользователю сначала будет предложено выбрать доктора, а потом откроется выбор услуг, которые он может оказать. При выключенной, наоборот - сначала выбор услуги, потом выбор доктора из списка тех, кто может эту услугу оказать');
$MESS['ANZ_APPOINTMENT_USE_TIME_STEPS'] = sprintf($hintTemplate , 'Использовать дополнительные интервалы времени при записи', 'При включенной опции, время для записи будет дано с интервалом, указанным в поле «Длительность интервала». Например у доктора свободно время с 10-00 до 12-00. При включенной опции, длительности интервала 15мин и длительности выбранной услуги в 60мин будут показаны следующие интервалы для записи: 10.00-11.00, 10.15-11.15, 10.30-11.30, 10.45-11.45, 11.00-12.00. А при выключенной опции: 10.00-11.00, 11.00-12.00.');
$MESS['ANZ_APPOINTMENT_TIME_STEP_DURATION'] = 'Длительность интервала (в минутах)';
$MESS['ANZ_APPOINTMENT_STRICT_CHECKING_RELATIONS'] = sprintf($hintTemplate , 'Строгий контроль привязки врача к клинике', 'При включенной опции, после выбора филиала будут доступны для выбора только те специализации, по которым есть врачи, привязанные к выбранному филиалу. Иначе будут показаны все специализации.');
$MESS['ANZ_APPOINTMENT_SHOW_DOCTORS_WITHOUT_DEPARTMENT'] = sprintf($hintTemplate , 'Показывать врачей, не привязанных к филиалу', 'В БИТ.УМЦ нет возможности привязать врача к нескольким филиалам, но иногда врач работает в разных филиалах посменно. В этом случае, у него нужно убрать привязку к филиалу в 1С. При включенной опции, врачи, не привязанные ни к одному филиалу, будут показаны во всех филиалах, если выбрана их специализация.');

/*Exchange options*/
$MESS['ANZ_APPOINTMENT_API_SETTINGS'] = "Настройки подключения";
$MESS['ANZ_APPOINTMENT_API_ADDRESS'] = "Адрес WSDL опубликованной базы 1С БИТ.УМЦ";
$MESS['ANZ_APPOINTMENT_API_LOGIN'] = "Логин пользователя 1С БИТ.УМЦ";
$MESS['ANZ_APPOINTMENT_API_PASSWORD'] = "Пароль пользователя 1С БИТ.УМЦ";
$MESS['ANZ_APPOINTMENT_EXCHANGE_MODE'] = "Режим обмена";
$MESS['ANZ_APPOINTMENT_EXCHANGE_SETTINGS'] = 'Настройки обмена';
$MESS['ANZ_APPOINTMENT_EXCHANGE_AGENT_ACTIVE'] = 'Активен';
$MESS['ANZ_APPOINTMENT_EXCHANGE_EXEC_INTERVAL'] = 'Периодичность обмена (в минутах)';
$MESS['ANZ_APPOINTMENT_EXCHANGE_CACHE_TTL'] = 'Время жизни кеша врачей, филиалов, услуг (в секундах)';
$MESS['ANZ_APPOINTMENT_EXCHANGE_NEXT_EXEC_DATE'] = 'Дата/Время следующего обмена';
$MESS['ANZ_APPOINTMENT_EXCHANGE_SCHEDULE_PERIOD'] = 'Период выгрузки расписания (в днях)';
$MESS['ANZ_APPOINTMENT_EXCHANGE_USE_SERVICES'] = sprintf($hintTemplate , 'Выгружать услуги и цены', 'Для выгрузки номенклатуры и прайсов необходимо <b>в 1С</b> создать настройку обмена с сайтом на необходимый филиал и указать прайс.<br>(Вкладка «Администрирование» – «Настройки обмена с сайтом»)');

$MESS['ANZ_APPOINTMENT_EXCHANGE_START_BTN'] = 'Провести обмен';
$MESS['ANZ_APPOINTMENT_EXCHANGE_MANUAL_DONE'] = 'Обмен выполнен в ручном режиме';

/*View options*/
$MESS['ANZ_APPOINTMENT_TAB_VIEW'] = "Внешний вид";
$MESS['ANZ_APPOINTMENT_TAB_TITLE_VIEW'] = "Визуальные настройки формы записи";
$MESS['ANZ_APPOINTMENT_LOGO_UPLOAD'] = "Логотип компании";
$MESS['ANZ_APPOINTMENT_JS_EXTENSION'] = "Варианты формы записи";
$MESS['ANZ_APPOINTMENT_JS_EXTENSION_SELECT'] = "Выбрать форму записи";
$MESS['ANZ_APPOINTMENT_JS_EXTENSION_BX_POPUP'] = "Первый вариант";
$MESS['ANZ_APPOINTMENT_JS_EXTENSION_FORM_VUE'] = "Второй вариант";
$MESS['ANZ_APPOINTMENT_JS_EXTENSION_CUSTOM'] = sprintf($hintTemplate , 'Использовать своё js-расширение', 'При заполненной опции стандартные варианты подключены не будут');
$MESS['ANZ_APPOINTMENT_MAIN_BTN_SETTINGS'] = 'Настройки кнопки "Онлайн-запись"';
$MESS['ANZ_APPOINTMENT_MAIN_BTN_BG_COLOR'] = "Цвет фона";
$MESS['ANZ_APPOINTMENT_MAIN_BTN_TEXT_COLOR'] = "Цвет текста";
$MESS['ANZ_APPOINTMENT_USE_CUSTOM_MAIN_BTN'] = "Использовать свою кнопку";
$MESS['ANZ_APPOINTMENT_CUSTOM_BTN_ID'] = 'Значение атрибута "id" собственной кнопки';
$MESS['ANZ_APPOINTMENT_FORM_COLORS_SETTINGS'] = "Настройки цветов формы";
$MESS['ANZ_APPOINTMENT_FORM_COLOR_MAIN'] = "Основной цвет формы";
$MESS['ANZ_APPOINTMENT_FORM_COLOR_FIELD'] = "Цвет полей ввода";
$MESS['ANZ_APPOINTMENT_FORM_COLOR_TEXT'] = "Цвет текста в полях ввода";
$MESS['ANZ_APPOINTMENT_FORM_COLOR_BTN'] = "Цвет кнопки на форме";
$MESS['ANZ_APPOINTMENT_FORM_COLOR_BTN_TEXT'] = "Цвет текста кнопки на форме";

/*Debug options*/
$MESS['ANZ_APPOINTMENT_DEBUG'] = 'Отладка';
$MESS['ANZ_APPOINTMENT_DEBUG_LOGS_TTL'] = 'Время хранения логов (в сутках)';

/*Access settings*/
$MESS['ANZ_APPOINTMENT_TAB_RIGHTS'] = "Настройки доступа";
$MESS['ANZ_APPOINTMENT_TAB_TITLE_RIGHTS'] = "Уровень доступа к модулю";

/*SOAP errors*/
$MESS["ANZ_APPOINTMENT_SOAP_EXT_NOT_FOUND"] = "Расширение php-soap не установлено";
$MESS['ANZ_APPOINTMENT_SOAP_AUTH_ERROR'] = "В настройках модуля не заполнены логин или пароль для 1С";
$MESS['ANZ_APPOINTMENT_SOAP_URL_ERROR'] = 'В настройках модуля адрес публикации базы 1С не заполнен или невалиден. Дополнительная информация об ошибке: "#ERROR#"';
$MESS['ANZ_APPOINTMENT_EXCHANGE_MODE_ERROR'] = "В настройках модуля не установлен режим обмена";

/*Data provider*/
$MESS['ANZ_APPOINTMENT_XML_PARSER_CLINIC_KEY']   = "Клиника";
$MESS['ANZ_APPOINTMENT_XML_PARSER_CLINIC_TITLE'] = "Наименование";
$MESS['ANZ_APPOINTMENT_XML_PARSER_CLINIC_UID']   = "УИД";

$MESS['ANZ_APPOINTMENT_XML_PARSER_EMPLOYEE_KEY']      = 'Сотрудник';
$MESS['ANZ_APPOINTMENT_XML_PARSER_ORGANIZATION']      = 'Организация';
$MESS['ANZ_APPOINTMENT_XML_PARSER_NAME']              = 'Имя';
$MESS['ANZ_APPOINTMENT_XML_PARSER_LAST_NAME']         = 'Фамилия';
$MESS['ANZ_APPOINTMENT_XML_PARSER_MIDDLE_NAME']       = 'Отчество';
$MESS['ANZ_APPOINTMENT_XML_PARSER_PHOTO']             = 'Фото';
$MESS['ANZ_APPOINTMENT_XML_PARSER_DESCRIPTION']       = 'КраткоеОписание';
$MESS['ANZ_APPOINTMENT_XML_PARSER_SPECIALTY']         = 'Специализация';
$MESS['ANZ_APPOINTMENT_XML_PARSER_SERVICES']          = 'ОсновныеУслуги';
$MESS['ANZ_APPOINTMENT_XML_PARSER_SERVICE']           = 'ОсновнаяУслуга';
$MESS['ANZ_APPOINTMENT_XML_PARSER_EMPLOYEE_DURATION'] = 'Продолжительность';

$MESS['ANZ_APPOINTMENT_XML_PARSER_CATALOG']     = 'Каталог';
$MESS['ANZ_APPOINTMENT_XML_PARSER_IS_FOLDER']   = 'ЭтоПапка';
$MESS['ANZ_APPOINTMENT_XML_PARSER_TITLE']       = 'Наименование';
$MESS['ANZ_APPOINTMENT_XML_PARSER_TYPE']        = 'Вид';
$MESS['ANZ_APPOINTMENT_XML_PARSER_ART_NUMBER']  = 'Артикул';
$MESS['ANZ_APPOINTMENT_XML_PARSER_PRICE']       = 'Цена';
$MESS['ANZ_APPOINTMENT_XML_PARSER_DURATION']    = 'Продолжительность';

$MESS['ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_FOR_SITE']           = 'ГрафикДляСайта';
$MESS['ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_EMPLOYEE_UID']       = 'СотрудникID';
$MESS['ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_EMPLOYEE_FULL_NAME'] = 'СотрудникФИО';
$MESS['ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_DURATION']           = 'ДлительностьПриема';
$MESS['ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_PERIODS']            = 'ПериодыГрафика';
$MESS['ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_PERIOD']             = 'ПериодГрафика';
$MESS['ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_FREE']               = 'СвободноеВремя';
$MESS['ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_BUSY']               = 'ЗанятоеВремя';
$MESS['ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_START']              = 'ВремяНачала';
$MESS['ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_END']                = 'ВремяОкончания';
$MESS['ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_DATE']               = 'Дата';
$MESS['ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_TIME_TYPE']          = 'ВидВремени';

$MESS['ANZ_APPOINTMENT_XML_PARSER_COMMON_RES_FLAG']     = 'Результат';
$MESS['ANZ_APPOINTMENT_XML_PARSER_COMMON_RES_DESC']     = 'ОписаниеРезультата';
$MESS['ANZ_APPOINTMENT_XML_PARSER_COMMON_ERROR_DESC']   = 'ОписаниеОшибки';
$MESS['ANZ_APPOINTMENT_XML_PARSER_COMMON_BOOKING_UID']  = 'УИД';

/*Confirmation*/
$MESS["ANZ_APPOINTMENT_CONFIRM_TYPE_ERROR"] = "Тип подтверждения записи не выбран или некорректен";
$MESS['ANZ_APPOINTMENT_CONFIRM_CODE_NOT_EXPIRED'] = 'Запрашивать код подтверждения можно не чаще, чем раз в минуту';
$MESS['ANZ_APPOINTMENT_CONFIRM_CODE_EXPIRED'] = 'Время действия кода подтверждения истекло';
$MESS['ANZ_APPOINTMENT_CONFIRM_CODE_INCORRECT'] = 'Неверный код подтверждения';

/*OneC errors*/
$MESS["ANZ_APPOINTMENT_DEMO_MODE_ERROR"] = "Demo mode error: ";
$MESS["ANZ_APPOINTMENT_REQUIRED_PARAMS_ERROR"] = "Not enough params to make appointment";
$MESS["ANZ_APPOINTMENT_RESERVE_ERROR"] = "Error on creating reserve in 1C. Reserve UID is empty";

/* Mail/Sms */
$MESS["ANZ_APPOINTMENT_SMS_CONFIRM_NAME"]      = "SMS-подтверждение";
$MESS["ANZ_APPOINTMENT_EMAIL_NOTE_NAME"]       = "Email оповещение о записи на приём";
$MESS["ANZ_APPOINTMENT_NOTE_DESC_TEXT"]        = "Текст сообщения";
$MESS["ANZ_APPOINTMENT_NOTE_DESC_EMAIL_TO"]    = "Email получателя";
$MESS["ANZ_APPOINTMENT_EMAIL_CONFIRM_NAME"]    = "Email-подтверждение";
$MESS["ANZ_APPOINTMENT_CONFIRM_DESC_CODE"]     = "Код подтверждения";
$MESS["ANZ_APPOINTMENT_MESSAGE_NOTE"] = "
Вы успешно записались на приём
Клиника: #CLINIC#
Специализация: #SPECIALTY#
Услуги: #SERVICE#
Врач: #DOCTOR#
Дата/время: #DATETIME#
ФИО: #NAME#
Номер телефона: #PHONE#
Комментарий: #COMMENT#
";

/*Wait list comment template*/
$MESS['ANZ_APPOINTMENT_WAITING_LIST_COMMENT'] =
    "Запрос с сайта
______________________________________ 
    
#FULL_NAME#
#PHONE#
#COMMENT#   
    
Желаемая дата: #DATE#
Желаемое время: #TIME#";

/* Appointment list component*/
$MESS["ANZ_APPOINTMENT_COMPONENT_ACCESS_DENIED"] = "Доступ к компоненту закрыт";
$MESS['ANZ_APPOINTMENT_TABLE_ID'] = "ID";
$MESS['ANZ_APPOINTMENT_TABLE_XML_ID'] = "GUID в 1С";
$MESS['ANZ_APPOINTMENT_TABLE_DATE_CREATE'] = "Дата создания";
$MESS['ANZ_APPOINTMENT_TABLE_DATETIME_VISIT'] = "Дата приёма";
$MESS['ANZ_APPOINTMENT_TABLE_DAYS_LEFT'] = "Дней до приёма";
$MESS['ANZ_APPOINTMENT_TABLE_CLINIC_TITLE'] = "Филиал";
$MESS['ANZ_APPOINTMENT_TABLE_SPECIALTY'] = "Специализация";
$MESS['ANZ_APPOINTMENT_TABLE_DOCTOR_NAME'] = "Врач";
$MESS['ANZ_APPOINTMENT_TABLE_SERVICE_TITLE'] = "Услуга";
$MESS['ANZ_APPOINTMENT_TABLE_PATIENT_NAME'] = "ФИО";
$MESS['ANZ_APPOINTMENT_TABLE_PATIENT_PHONE'] = "Телефон";
$MESS['ANZ_APPOINTMENT_TABLE_PATIENT_EMAIL'] = "Email";
$MESS['ANZ_APPOINTMENT_TABLE_COMMENT'] = "Комментарий";
$MESS['ANZ_APPOINTMENT_TABLE_STATUS_1C'] = "Статус в 1С";
$MESS['ANZ_APPOINTMENT_TABLE_USER_ID'] = "ID пользователя";
$MESS['ANZ_APPOINTMENT_BTN_DELETE_TEXT'] = "Удалить запись";
$MESS['ANZ_APPOINTMENT_BTN_UPDATE_STATUS_TEXT'] = "Обновить статус";

/* Appointment execute-btn component*/
$MESS["ANZ_APPOINTMENT_EXCHANGE_START_BTN"] = "Провести обмен";
$MESS["ANZ_APPOINTMENT_EXCHANGE_MANUAL_DONE"] = "Обмен выполнен в ручном режиме";
$MESS["ANZ_APPOINTMENT_EXCHANGE_MANUAL_ERROR_DEMO"] = "Обмен не выполнен, так как в настройках модуля включен демо-режим";

/* Appointment check-api-btn component*/
$MESS['ANZ_APPOINTMENT_API_CHECK_BTN'] = 'Проверить подключение';
$MESS['ANZ_APPOINTMENT_API_CHECK_NOT_APPLIED'] = 'Проверка не производилась';
$MESS['ANZ_APPOINTMENT_API_CHECK_SUCCESS'] = 'Подключение успешно';
$MESS['ANZ_APPOINTMENT_API_CHECK_ERROR'] = 'Ошибка при подключении: ';