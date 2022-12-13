<?php

namespace GroupThr3e\AJExchange\Models;

class Office extends ModelBase
{
    protected int $officeId;
    protected string $officeName;
    protected string $address;

    /**
     * @param int $officeId
     * @param string $officeName
     * @param string $address
     */
    public function __construct(int $officeId, string $officeName, string $address)
    {
        $this->officeId = $officeId;
        $this->officeName = $officeName;
        $this->address = $address;
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