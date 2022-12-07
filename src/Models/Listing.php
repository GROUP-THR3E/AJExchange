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
     * @return int the id of the listing
     */
    public function getListingId(): int
    {
        return $this->listingId;
    }

    /**
     * @return string the name of the listing
     */
    public function getListingName(): string
    {
        return $this->listingName;
    }

    /**
     * @return string the description of the litsing
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array the tags the user assigned to the listing
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return string the type of listing (sale,swap,giving away)
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string the date the listing was submitted
     */
    public function getDateListed(): string
    {
        return $this->dateListed;
    }

    /**
     * @return int the id of the user who submitted the listing
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return User|null the user who submitted the listing. May be null if the user table wasn't joined during data access
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