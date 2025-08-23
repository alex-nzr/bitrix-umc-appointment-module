<?php
/*
 * ==================================================
 * This file is part of project bitrix.firstbit.umc
 * 22.08.2025
 * ==================================================
*/
namespace ANZ\Appointment\Core\Trait;

trait Singleton
{
    protected static array $instances = [];

    public static function getInstance()
    {
        if (!key_exists(static::class, self::$instances)
            || empty(static::$instances[static::class])
            || !(static::$instances[static::class] instanceof static)
        )
        {
            static::$instances[static::class] = new static();
        }
        return static::$instances[static::class];
    }

    protected function __construct(){}

    final public function __clone()
    {
    }

    final public function __wakeup()
    {
    }

    final public function __sleep()
    {
    }
}