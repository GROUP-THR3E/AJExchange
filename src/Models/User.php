<?php

class User
{
    protected int $userId;
    protected string $email;
    protected string $fullName;
    protected string $role;
    protected int $officeId;
    protected Office $office;

    public function __construct(array $dbRow)
    {
        $this->userId = $dbRow['userId'];
        $this->email = $dbRow['email'];
        $this->fullName = $dbRow['fullName'];
        $this->role = $dbRow['role'];
        $this->officeId = $dbRow['officeId'];
    }

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

    /**
     * Sets office from db result
     * @param array $dbRow the database result to use
     */
    public function setOffice(array $dbRow): void
    {
        $this->office = new Office($dbRow);
    }
}