<?php



if (is_file($_SERVER["DOCUMENT_ROOT"]."/local/modules/firstbit.appointment/admin/firstbit.app.settings.page.php")){
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/modules/firstbit.appointment/admin/firstbit.app.settings.page.php");
}
elseif (is_file($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/firstbit.appointment/admin/firstbit.app.settings.page.php")){
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/firstbit.appointment/admin/firstbit.app.settings.page.php");
}
