<?php
namespace ANZ\Appointment\Core\Contract\Page;

interface IPage
{
    public function checkAccess();
    public function draw();
    public function isAdminPage() : bool;
}
