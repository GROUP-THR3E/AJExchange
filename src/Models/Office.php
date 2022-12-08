<?php

namespace GroupThr3e\AJExchange\Models;

class Office
{
    protected int $officeId;
    protected string $officeName;
    protected string $address;

    public function __construct(array $dbRow)
    {
        $this->officeId = $dbRow['officeId'];
        $this->officeName = $dbRow['officeName'];
        $this->address = $dbRow['address'];
    }

    /**
     * @return int the id of the office
     */
    public function getOfficeId(): int
    {
        return $this->officeId;
    }

    /**
     * @return string the name of the office
     */
    public function getOfficeName(): string
    {
        return $this->officeName;
    }

    /**
     * @return string the full address of the office
     */
    public function getAddress(): string
    {
        return $this->address;
    }
}