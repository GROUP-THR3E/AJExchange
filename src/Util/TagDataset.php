<?php

namespace GroupThr3e\AJExchange\Util;

use GroupThr3e\AJExchange\Models\Tag;

class TagDataset extends DatasetBase
{
    /**
     * @return Tag[] a list of all tags in the database
     */
    public function getTags(): array
    {
        $statement = $this->dbHandle->query('SELECT * FROM Tag');
        $tags = [];
        foreach ($statement->fetchAll() as $result) {
            $tags = new Tag($result);
        }

        return $tags;
    }
}