<?php
if (is_file($_SERVER["DOCUMENT_ROOT"]."/local/modules/firstbit.appointment/admin/page.php")){
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/modules/firstbit.appointment/admin/page.php");
}
elseif (is_file($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/firstbit.appointment/admin/page.php")){
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/firstbit.appointment/admin/page.php");
}