<?php

namespace Skop\Core;

trait Singleton
{
    protected static $instance;

    final public static function instance()
    {
        return isset(static::$instance)
            ? static::$instance
            : static::$instance = new static;
    }
}
