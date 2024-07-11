<?php

namespace Skop\Core;

use PDO;

class Db
{
    private static ?PDO $instance = null;

    public static function instance(): PDO
    {
        if (Db::$instance != null)
            return Db::$instance;
        Db::$instance = new PDO('mysql:host=localhost; dbname=skop', 'root', '');
        Db::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return Db::$instance;
    }
}
