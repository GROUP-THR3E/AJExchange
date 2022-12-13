<?php

namespace GroupThr3e\AJExchange\Util;

use GroupThr3e\AJExchange\Models\User;

class UserDataset extends DatasetBase
{
    public function getUser (int $userId): User
    {
        $query = 'SELECT * FROM User INNER JOIN Office ON User.officeId = Office.officeId WHERE userId = :userId';
        $statement = $this->dbHandle->prepare($query);
        $statement->execute(['userId' => $userId]);
        return $this->createModel(User::class, $statement->fetch());
    }

    /**
     * Retrieves the user with the matching email
     * @param string $email the email of the user to retrieve
     * @return User|null the user, null if the user wasn't found
     */
    public function getUserByEmail(string $email): ?User
    {
        $statement = $this->dbHandle->prepare('SELECT * FROM User INNER JOIN Office ON User.officeId = Office.officeId WHERE email = :email');
        $statement->execute(['email' => $email]);
        $result = $statement->fetch();
        if ($result === false) return null;

        return $this->createModel(User::class, $result);
    }
}