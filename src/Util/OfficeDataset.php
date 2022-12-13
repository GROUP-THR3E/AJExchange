<?php

namespace GroupThr3e\AJExchange\Util;

use GroupThr3e\AJExchange\Models\Office;

class OfficeDataset extends DatasetBase
{
    public function getOffice(int $officeId): Office
    {
        $query = 'SELECT * FROM Office WHERE officeId = :officeId';
        $statement = $this->dbHandle->prepare($query);
        $statement->execute(['officeId' => $officeId]);
        return $this->createModel(Office::class, $statement->fetch());
    }

    public function getOffices(): array
    {
        $query = 'SELECT * FROM Office';
        $statement = $this->dbHandle->prepare($query);
        $statement->execute([]);
        $result = [];
        foreach ($statement->fetchAll() as $item) {
            $result[] = $this->createModel(Office::class, $item);
        }
        return $result;
    }
}