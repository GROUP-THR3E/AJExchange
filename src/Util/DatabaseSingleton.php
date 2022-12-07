<?php

namespace GroupThr3e\AJExchange\Util;

use PDO;

class DatabaseSingleton
{
    private static PDO $dbHandle;

    public static function getHandle()
    {
        if (self::$dbHandle === null) {
            $config = parse_ini_file('../config.ini');
            self::$dbHandle = new PDO($config['dsn'], $config['username'], $config['password']);
        }

        return self::$dbHandle;
    }
}