<?php

namespace GroupThr3e\AJExchange\Models;

class Tag extends ModelBase
{
    protected string $tag;
    protected int $taggedListings;

    /**
     * @return string the tag
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * @return int the number of unordered approved listings with the tag
     */
    public function getTaggedListings(): int
    {
        return $this->taggedListings;
    }


}