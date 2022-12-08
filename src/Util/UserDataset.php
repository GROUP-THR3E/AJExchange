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

    /**
     * Retrieves the user with the matching email
     * @param string $email the email of the user to retrieve
     * @return User|null the user, null if the user wasn't found
     */
    public function getUserByEmail(string $email): ?User
    {
        $statement = $this->dbHandle->prepare('SELECT * FROM User WHERE email = :email');
        $statement->execute(['email' => $email]);
        $result = $statement->fetch();
        return $result !== false ? new User($result) : null;
    }
}