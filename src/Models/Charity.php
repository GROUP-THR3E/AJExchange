<?php

namespace GroupThr3e\AJExchange\Models;

class Charity extends ModelBase
{
    protected int $charityId;
    protected string $charityName;

    /**
     * @return int the id of the charity
     */
    public function getCharityId(): int
    {
        return $this->charityId;
    }

    /**
     * @return string the name of the charity
     */
    public function getCharityName(): string
    {
        return $this->charityName;
    }
}