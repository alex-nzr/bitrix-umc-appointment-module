<?php
if (is_file($_SERVER["DOCUMENT_ROOT"]."/local/modules/anz.appointment/admin/pages/anz.app.settings.page.php")){
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/modules/anz.appointment/admin/pages/anz.app.settings.page.php");
}
elseif (is_file($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/anz.appointment/admin/pages/anz.app.settings.page.php")){
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/anz.appointment/admin/pages/anz.app.settings.page.php");
}