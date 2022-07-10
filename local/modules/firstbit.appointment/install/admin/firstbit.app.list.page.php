<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - firstbit.app.list.page.php
 * 10.07.2022 22:37
 * ==================================================
 */
if (is_file($_SERVER["DOCUMENT_ROOT"]."/local/modules/firstbit.appointment/admin/firstbit.app.list.page.php")){
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/modules/firstbit.appointment/admin/firstbit.app.list.page.php");
}
elseif (is_file($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/firstbit.appointment/admin/firstbit.app.list.page.php")){
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/firstbit.appointment/admin/firstbit.app.list.page.php");
}
