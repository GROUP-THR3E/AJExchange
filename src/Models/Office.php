<?php

class Office
{
    protected int $officeId;
    protected string $name;
    protected string $address;

    public function __construct(array $dbRow)
    {
        $this->officeId = $dbRow['officeId'];
        $this->name = $dbRow['name'];
        $this->address = $dbRow['address'];
    }
}