<?php

namespace GroupThr3e\AJExchange\Models;

class Listing extends ModelBase
{
    protected int $listingId;
    protected string $listingName;
    protected string $description;
    protected ?float $price;
    protected ?string $desiredItem;
    protected array $tags;
    protected string $type;
    protected string $dateListed;
    protected int $userId;
    protected ?User $user;

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
     * @return string the description of the listing
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return float|null the price of the listing, null if the listing is not a sale
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @return string|null the item the submitter is willing to exchange the listing for, null if the listing is not an exchange
     */
    public function getDesiredItem(): ?string
    {
        return $this->desiredItem;
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
}