<?php

namespace GroupThr3e\AJExchange\Util;

use PDO;

class DatasetBase
{
    protected PDO $dbHandle;

    public function __construct()
    {
        $this->dbHandle = DatabaseSingleton::getHandle();
    }
}