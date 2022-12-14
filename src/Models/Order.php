<?php

namespace GroupThr3e\AJExchange\Models;

class Order extends ModelBase
{
    protected int $orderId;
    protected string $orderDate;
    protected int $userId;
    // TODO - Currently there is no navigation property for User, this is because the current mapping system cannot
    // distinguish different properties inheriting ModelBase of the same type
    protected int $listingId;
    protected ?Listing $listing;

    /**
     * @return int the id of the purchase
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @return string the date the order was made
     */
    public function getOrderDate(): string
    {
        return $this->orderDate;
    }

    /**
     * @return int the id user who made the purchase
     */
    public function getUserId(): int
    {
        return $this->userId;
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