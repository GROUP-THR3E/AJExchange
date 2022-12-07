<?php

namespace GroupThr3e\AJExchange\Util;

use GroupThr3e\AJExchange\Models\User;

class UserDataset extends DatasetBase
{
    public function getUser (int $userId): User
    {
        $query = 'SELECT * FROM User WHERE userId = :userId';
        $statement = $this->dbHandle->prepare($query);
        $statement->execute(['userId' => $userId]);
        return new User($statement->fetch());
    }
}