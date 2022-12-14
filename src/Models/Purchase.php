<?php

namespace GroupThr3e\AJExchange\Models;

class Purchase
{
    protected int $purchaseId;
    protected int $userId;
    protected ?User $user;
    protected int $listingId;
    protected ?Listing $listing;

    /**
     * @return int the id of the purchase
     */
    public function getPurchaseId(): int
    {
        return $this->purchaseId;
    }

    /**
     * @return int the id user who made the purchase
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return User|null the id user who purchased the item, null if the table wasn't joined when retrieved
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return int the id of the listing that was purchased
     */
    public function getListingId(): int
    {
        return $this->listingId;
    }

    /**
     * @return Listing|null the listing that was purchased, null if the table wasn't joined when retrieved
     */
    public function getListing(): ?Listing
    {
        return $this->listing;
    }
}