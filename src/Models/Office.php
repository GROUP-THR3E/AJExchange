<?php

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
}