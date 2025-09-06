<?php

namespace Bitrix\Main\Data {
    class Cache {
        public static function createInstance(){ return new self(); }
        public function initCache($ttl, $id, $dir){ return false; }
        public function getVars(){ return null; }
        public function startDataCache(){ return true; }
        public function endDataCache($data){ }
    }
}

namespace Bitrix\Main\ORM{
    class Entity
    {

    }
}

namespace Bitrix\Main\ORM\Data{
    class DataManager
    {

    }
    class AddResult{}
    class DeleteResult{}
    class UpdateResult{}
}

namespace {
    require_once __DIR__ . '/../vendor/autoload.php';
    date_default_timezone_set('Europe/Moscow');
}
