<?php

namespace GroupThr3e\AJExchange\Models;

class User extends ModelBase
{
    protected int $userId;
    protected string $email;
    protected string $password;
    protected string $fullName;
    protected string $role;
    protected int $officeId;
    protected Office $office;


    /**
     * @return int the id of the user
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string the email of the user
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string the hashed password of the user
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string the full name of the uesr
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @return string the user's role (user or admin)
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @return int the id of the office the user is assigned to
     */
    public function getOfficeId(): int
    {
        return $this->officeId;
    }

    /**
     * @return Office|null the office the user is assigned to. This may be null if it was not loaded during the SQL query
     */
    public function getOffice(): ?Office
    {
        return $this->office;
    }
}