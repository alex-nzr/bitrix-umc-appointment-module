<?php
$MESS['ANZ_APPOINTMENT_MODULE_SETTINGS'] = "Настройки модуля";
$MESS['ANZ_APPOINTMENT_API_SETTINGS'] = "Подключение к API";
$MESS['ANZ_APPOINTMENT_API_ADDRESS'] = "Адрес WSDL опубликованной базы 1С БИТ.УМЦ";
$MESS['ANZ_APPOINTMENT_API_LOGIN'] = "Логин пользователя 1С БИТ.УМЦ";
$MESS['ANZ_APPOINTMENT_API_PASSWORD'] = "Пароль пользователя 1С БИТ.УМЦ";
$MESS['ANZ_APPOINTMENT_OTHER_SETTINGS'] = "Прочие настройки";
$MESS['ANZ_APPOINTMENT_SCHEDULE_PERIOD'] = "Период выгрузки расписания (дней)";
$MESS['ANZ_APPOINTMENT_DEFAULT_DURATION'] = "Длительность приёма по умолчанию, если не указана в 1С (секунд)";
$MESS['ANZ_APPOINTMENT_USE_NOMENCLATURE'] = "Выгружать номенклатуру и цены.";
$MESS['ANZ_APPOINTMENT_USE_NOMENCLATURE_WARNING'] = "Внимание! Для выгрузки номенклатуры и прайсов необходимо <b>в 1С</b> создать настройку обмена с сайтом на необходимый филиал и указать прайс.<br>(Вкладка «Администрирование» – «Настройки обмена с сайтом»)";
$MESS['ANZ_APPOINTMENT_SELECT_DOCTOR_BEFORE_SERVICE'] = 'Выбирать сначала доктора, потом услугу';
$MESS['ANZ_APPOINTMENT_SELECT_DOCTOR_BEFORE_SERVICE_NOTE'] = 'При включенной опции, 
                                                                пользователю сначала будет предложено выбрать доктора, 
                                                                а потом откроется выбор услуг, которые он может оказать. 
                                                                При выключенной, наоборот - сначала выбор услуги, 
                                                                потом выбор доктора из списка тех, кто может эту услугу оказать';

$MESS['ANZ_APPOINTMENT_USE_TIME_STEPS'] = 'Использовать дополнительные интервалы времени при записи';
$MESS['ANZ_APPOINTMENT_USE_TIME_STEPS_NOTE'] = 'При включенной опции, время для записи будет дано с интервалом,
                                                    указанным в поле "Длительность интервала". Например у доктора свободно время с 10-00 до 12-00.
                                                    При включенной опции, длительности интервала 15мин и длительности выбранной услуги в 60мин 
                                                    будут показаны следующие интервалы для записи: 
                                                    10.00-11.00, 10.15-11.15, 10.30-11.30, 10.45-11.45, 11.00-12.00.
                                                    А при выключенной опции: 10.00-11.00, 11.00-12.00.';
$MESS['ANZ_APPOINTMENT_TIME_STEP_DURATION'] = 'Длительность интервала (в минутах)';

$MESS['ANZ_APPOINTMENT_STRICT_CHECKING_RELATIONS'] = 'Строгий контроль привязки врача к клинике';
$MESS['ANZ_APPOINTMENT_STRICT_CHECKING_RELATIONS_NOTE'] = 'При включенной опции, после выбора филиала будут доступны
                                                               для выбора только те специализации, по которым есть врачи, 
                                                               привязанные к выбранному филиалу. Иначе будут показаны все специализации.';

$MESS['ANZ_APPOINTMENT_SHOW_DOCTORS_WITHOUT_DEPARTMENT'] = 'Показывать врачей, не привязанных к филиалу';
$MESS['ANZ_APPOINTMENT_SHOW_DOCTORS_WITHOUT_DEPARTMENT_NOTE'] = 'В БИТ.УМЦ нет возможности привязать врача к нескольким филиалам, но
                                                                       иногда врач работает в разных филиалах посменно.  
                                                                       В этом случае, у него нужно убрать привязку к филиалу в 1С.
                                                                       При включенной опции, врачи, не привязанные ни к одному филиалу,
                                                                       будут показаны во всех филиалах, если выбрана их специализация.';

$MESS['ANZ_APPOINTMENT_PRIVACY_PAGE_URL'] = 'Ссылка на политику конфиденциальности сайта<br>(необходима для размещения на форме записи)';
$MESS['ANZ_APPOINTMENT_USE_EMAIL_NOTE'] = 'Отправлять информацию о записи на email';
$MESS['ANZ_APPOINTMENT_USE_WAITING_LIST'] = "Запись в лист ожидания";
$MESS['ANZ_APPOINTMENT_USE_WAITING_LIST_NOTE'] = 'При включенной опции запись в 1С будет создаваться не как документ "Заявка", а как "Лист ожидания"';

$MESS['ANZ_APPOINTMENT_CONFIRM_WITH'] = "Включить подтверждение записи";
$MESS['ANZ_APPOINTMENT_CONFIRM_WITH_NOTE'] = 'Для смс-подтверждения необходимо выбрать и настроить провайдера SMS в настройках модуля "Служба сообщений". Затем установить отправителя по умолчанию в настройках главного модуля.';
$MESS['ANZ_APPOINTMENT_CONFIRM_WITH_PHONE'] = "По СМС";
$MESS['ANZ_APPOINTMENT_CONFIRM_WITH_EMAIL'] = "По email";
$MESS['ANZ_APPOINTMENT_CONFIRM_WITH_NONE']  = "Выключено";

$MESS['ANZ_APPOINTMENT_TAB_VIEW'] = "Внешний вид";
$MESS['ANZ_APPOINTMENT_TAB_TITLE_VIEW'] = "Визуальные настройки формы записи";
$MESS['ANZ_APPOINTMENT_LOGO_UPLOAD'] = "Логотип компании";
$MESS['ANZ_APPOINTMENT_MAIN_BTN_SETTINGS'] = "Настройки кнопки вызова окна записи";
$MESS['ANZ_APPOINTMENT_USE_CUSTOM_MAIN_BTN'] = "Использовать свою кнопку для вызова всплывающего окна";
$MESS['ANZ_APPOINTMENT_CUSTOM_BTN_ID'] = 'Значение атрибута "id" собственной кнопки';
$MESS['ANZ_APPOINTMENT_MAIN_BTN_BG_COLOR'] = "Цвет фона штатной кнопки";
$MESS['ANZ_APPOINTMENT_MAIN_BTN_TEXT_COLOR'] = "Цвет текста штатной кнопки";

$MESS['ANZ_APPOINTMENT_FORM_COLORS_SETTINGS'] = "Настройки цветов формы";
$MESS['ANZ_APPOINTMENT_FORM_COLOR_MAIN'] = "Основной цвет формы";
$MESS['ANZ_APPOINTMENT_FORM_COLOR_FIELD'] = "Цвет полей ввода";
$MESS['ANZ_APPOINTMENT_FORM_COLOR_TEXT'] = "Цвет текста в полях ввода";
$MESS['ANZ_APPOINTMENT_FORM_COLOR_BTN'] = "Цвет кнопки на форме";
$MESS['ANZ_APPOINTMENT_FORM_COLOR_BTN_TEXT'] = "Цвет текста кнопки на форме";

$MESS['ANZ_APPOINTMENT_USE_AUTO_INJECTING'] = "Автоматическое подключение";
$MESS['ANZ_APPOINTMENT_USE_AUTO_INJECTING_ON'] = "Включить";
$MESS['ANZ_APPOINTMENT_USE_AUTO_INJECTING_NOTE'] = 'При включенной опции скрипты формы записи будут добавлены 
                                                         на всех страницах сайта автоматически. Если указан id собственной кнопки,
                                                         то вызов формы будет привязан к ней, иначе на страницах появится штатная кнопка.<br>
                                                         При отключенной опции, нужно самостоятельно разместить на сайте компонент записи.';


$MESS['ANZ_APPOINTMENT_USE_DEMO_MODE']    = "Демо режим";
$MESS['ANZ_APPOINTMENT_USE_DEMO_MODE_ON'] = "Включить демо-режим";

$MESS['ANZ_APPOINTMENT_TAB_RIGHTS'] = "Настройки доступа";
$MESS['ANZ_APPOINTMENT_TAB_TITLE_RIGHTS'] = "Уровень доступа к модулю";