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

namespace Bitrix\Main\Config {
    class Option {
        private static array $values = [];

        public static function get(string $moduleId, string $name, string $default = ''): string
        {
            return self::$values[$moduleId][$name] ?? $default;
        }

        public static function set(string $moduleId, string $name, string $value): void
        {
            self::$values[$moduleId][$name] = $value;
        }
    }
}

namespace Bitrix\Main\ORM{
    class Entity
    {

    }
}

namespace Bitrix\Main\ORM\Objectify {
    class EntityObject {
        protected array $fields = [];

        public function has(string $name): bool
        {
            return array_key_exists($name, $this->fields);
        }

        public function get(string $name): mixed
        {
            return $this->fields[$name] ?? null;
        }

        public function set(string $name, mixed $value): static
        {
            $this->fields[$name] = $value;
            return $this;
        }

        public function getId(): mixed
        {
            return $this->fields['ID'] ?? null;
        }

        public function setId(mixed $id): static
        {
            $this->fields['ID'] = $id;
            return $this;
        }
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
