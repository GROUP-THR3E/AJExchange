<?php

namespace GroupThr3e\AJExchange\Models;

class Office extends ModelBase
{
    protected int $officeId;
    protected string $officeName;
    protected string $address;

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