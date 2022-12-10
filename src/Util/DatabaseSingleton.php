<?php

namespace GroupThr3e\AJExchange\Util;

use PDO;

class DatabaseSingleton
{
    private static ?PDO $dbHandle = null;

    public static function getHandle(): ?PDO
    {
        if (self::$dbHandle === null) {
            $config = parse_ini_file('config.ini');
            self::$dbHandle = new PDO($config['dsn'], $config['username'], $config['password']);
            self::$dbHandle->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }

        return self::$dbHandle;
    }
}