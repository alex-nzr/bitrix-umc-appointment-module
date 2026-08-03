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

namespace Bitrix\Main {
    class ArgumentException extends \Exception {}
    class AccessDeniedException extends \Exception {}
    class SystemException extends \Exception {}

    class Event {
        public function __construct(
            private string $moduleId,
            private string $eventName,
            private mixed $parameters = null
        ) {
        }

        public function send(): void
        {
        }

        public function getParameters(): mixed
        {
            return $this->parameters;
        }

        public function getResults(): array
        {
            return [];
        }
    }

    class EventResult {
        public const ERROR = 'ERROR';
        public const SUCCESS = 'SUCCESS';
        public const UNDEFINED = 'UNDEFINED';
    }

    class Application {
        private static ?self $instance = null;
        private Session $session;

        private function __construct()
        {
            $this->session = new Session();
        }

        public static function getInstance(): self
        {
            return self::$instance ??= new self();
        }

        public function getSession(): Session
        {
            return $this->session;
        }
    }

    class Session {
        private array $data = [];

        public function get(string $name): mixed
        {
            return $this->data[$name] ?? null;
        }

        public function set(string $name, mixed $value): void
        {
            $this->data[$name] = $value;
        }
    }
}

namespace Bitrix\Main\Config {
    class Configuration {
        private static array $values = [];

        public static function getValue(string $name): mixed
        {
            return self::$values[$name] ?? null;
        }

        public static function setValue(string $name, mixed $value): void
        {
            self::$values[$name] = $value;
        }
    }

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

namespace Bitrix\Main\Security {
    class SecurityException extends \Exception {}

    class Cipher {
        public function encrypt(string $value, string $key): string
        {
            $iv = random_bytes(16);
            $cipher = openssl_encrypt($value, 'AES-256-CBC', hash('sha256', $key, true), OPENSSL_RAW_DATA, $iv);
            if ($cipher === false)
            {
                throw new SecurityException('Encryption failed');
            }

            return $iv . hash_hmac('sha256', $iv . $cipher, $key, true) . $cipher;
        }

        public function decrypt(string $value, string $key): string
        {
            if (strlen($value) < 48)
            {
                throw new SecurityException('Invalid encrypted value');
            }

            $iv = substr($value, 0, 16);
            $signature = substr($value, 16, 32);
            $cipher = substr($value, 48);
            if (!hash_equals(hash_hmac('sha256', $iv . $cipher, $key, true), $signature))
            {
                throw new SecurityException('Invalid encrypted value');
            }

            $decrypted = openssl_decrypt($cipher, 'AES-256-CBC', hash('sha256', $key, true), OPENSSL_RAW_DATA, $iv);
            if ($decrypted === false)
            {
                throw new SecurityException('Decryption failed');
            }

            return $decrypted;
        }
    }
}

namespace Bitrix\Main\DI {
    class ServiceLocator {
        private static ?self $instance = null;
        private array $items = [];

        public static function getInstance(): self
        {
            return self::$instance ??= new self();
        }

        public function has(string $identifier): bool
        {
            return array_key_exists($identifier, $this->items);
        }

        public function addInstance(string $identifier, mixed $instance): void
        {
            $this->items[$identifier] = $instance;
        }

        public function get(string $identifier): mixed
        {
            return $this->items[$identifier] ?? null;
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
