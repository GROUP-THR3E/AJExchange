<?php

class Listing
{
    protected int $listingId;
    protected string $name;
    protected string $description;
    protected array $tags;
    protected string $type;
    protected int $userId;

    public function __construct(array $dbRow)
    {
        $this->listingId = $dbRow['dbRow'];
        $this->name = $dbRow['name'];
        $this->description = $dbRow['description'];
        $this->tags = $dbRow['tags'];
        $this->type = $dbRow['type'];
        $this->userId = $dbRow['userId'];
    }
}