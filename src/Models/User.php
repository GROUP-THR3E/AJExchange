<?php

class User
{
    public int $userId;
    public string $email;
    public string $fullName;
    public string $role;
    public int $officeId;

    public function __construct(array $dbRow)
    {
        $this->userId = $dbRow['userId'];
        $this->email = $dbRow['email'];
        $this->fullName = $dbRow['fullName'];
        $this->role = $dbRow['role'];
        $this->officeId = $dbRow['officeId'];
    }
}