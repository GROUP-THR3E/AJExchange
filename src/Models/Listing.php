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
    public array $imageUrls;
    protected string $type;
    protected string $dateListed;
    protected string $approvalStatus;
    protected int $userId;
    protected ?User $user;
    protected ?int $orderId;
    protected ?int $charityId;
    protected ?Charity $charity;

    public function __construct(array $dbRow)
    {
        parent::__construct($dbRow);
        if (isset($dbRow['imageUrls'])) $this->imageUrls = explode(',', $dbRow['imageUrls']);
        if (isset($dbRow['tags'])) {
            if (!empty($dbRow['tags'])) $this->tags = explode(',', $dbRow['tags']);
            else $this->tags = [];
        }
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
     * @return string[] an array of 0-5 image urls
     */
    public function getImageUrls(): array
    {
        return $this->imageUrls;
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
     * @return string the approval status of the listing (approved/denied/pending)
     */
    public function getApprovalStatus(): string
    {
        return $this->approvalStatus;
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
     * @return int|null the id of the order of this listing, null if not yet purchased
     */
    public function getOrderId(): ?int
    {
        return $this->orderId ?? null;
    }

    /**
     * @return int|null the id of the charity for the proceeds to go to, null if none is set
     */
    public function getCharityId(): ?int
    {
        return $this->charityId;
    }

    /**
     * @return Charity|null the charity for the proceeds to go to, null if none is set
     */
    public function getCharity(): ?Charity
    {
        return $this->charity;
    }
}