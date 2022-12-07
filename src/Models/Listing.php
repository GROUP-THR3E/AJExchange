<?php

class Listing
{
    protected int $listingId;
    protected string $listingName;
    protected string $description;
    protected array $tags;
    protected string $type;
    protected string $dateListed;
    protected int $userId;
    protected ?User $user;

    public function __construct(array $dbRow)
    {
        $this->listingId = $dbRow['dbRow'];
        $this->listingName = $dbRow['listingName'];
        $this->description = $dbRow['description'];
        $this->tags = $dbRow['tags'];
        $this->type = $dbRow['type'];
        $this->dateListed = $dbRow['dateListed'];
        $this->userId = $dbRow['userId'];
    }

    /**
     * @return int
     */
    public function getListingId(): int
    {
        return $this->listingId;
    }

    /**
     * @return string
     */
    public function getListingName(): string
    {
        return $this->listingName;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getDateListed(): string
    {
        return $this->dateListed;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Sets user with values from a database result
     * @param array $dbRow the database result to use
     */
    public function setUser(array $dbRow): void
    {
        $this->user = new User($dbRow);
    }
}