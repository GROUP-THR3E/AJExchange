<?php

namespace GroupThr3e\AJExchange\Util;

use GroupThr3e\AJExchange\Models\Charity;

class CharityDataset extends DatasetBase
{
    public function getCharities()
    {
        $statement = $this->dbHandle->query('SELECT * FROM Charity');
        $statement->execute();
        $results = [];
        foreach ($statement->fetchAll() as $result) {
            $results[] = new Charity($result);
        }

        return $results;
    }
}