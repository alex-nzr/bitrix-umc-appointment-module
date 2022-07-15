<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - anz.app.settings.page.php
 * 10.07.2022 22:37
 * ==================================================
 */
if (is_file($_SERVER["DOCUMENT_ROOT"]."/local/modules/anz.appointment/admin/anz.app.settings.page.php")){
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/modules/anz.appointment/admin/anz.app.settings.page.php");
}
elseif (is_file($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/anz.appointment/admin/anz.app.settings.page.php")){
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/anz.appointment/admin/anz.app.settings.page.php");
}
