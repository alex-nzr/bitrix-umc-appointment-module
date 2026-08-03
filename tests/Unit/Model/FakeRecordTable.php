<?php

namespace ANZ\Appointment\Tests\Unit\Model;

use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\DeleteResult;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\ORM\Entity;

class FakeRecordTable
{
    public static function add(array $data): AddResult
    {
        $res = new AddResult;
        $res->setId(999);
        $res->setPrimary([999]);
        return $res;
    }

    public static function update($primary, array $data): UpdateResult
    {
        return new UpdateResult;
    }

    public static function delete($primary): DeleteResult
    {
        return new DeleteResult;
    }

    public static function getEntity()
    {
        return new Entity();
    }
}
