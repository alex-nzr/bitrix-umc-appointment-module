<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 08.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Internals\Contract\Page;

interface IPage
{
    public function checkAccess();
    public function draw();
    public function isAdminPage() : bool;
}