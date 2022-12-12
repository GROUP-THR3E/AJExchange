<?php

namespace GroupThr3e\AJExchange\Util;

use GroupThr3e\AJExchange\Constants\ListingType;
use GroupThr3e\AJExchange\Models\HomepageData;
use GroupThr3e\AJExchange\Models\Listing;

class HomeDataset extends DatasetBase
{
    public function getHomepage(): HomepageData
    {
        $listingQuery =
            'SELECT * FROM Listing
             LEFT JOIN ListingImage ON Listing.listingId = ListingImage.listingId
             WHERE type = :type
             AND imageIndex = 1
             LIMIT 4';

        $statement = $this->dbHandle->prepare($listingQuery);

        $statement->execute(['type' => ListingType::SALE]);
        $saleResults = [];
        foreach ($statement->fetchAll() as $result) {
            $saleResults[] = new Listing($result, [$result['filename']]);
        }

        $statement->execute(['type' => ListingType::EXCHANGE]);
        $exchangeResults = [];
        foreach ($statement->fetchAll() as $result) {
            $exchangeResults[] = new Listing($result, [$result['filename']]);
        }

        $statement->execute(['type' => ListingType::GIVEAWAY]);
        $giveawayResults = [];
        foreach ($statement->fetchAll() as $result) {
            $giveawayResults[] = new Listing($result, [$result['filename']]);
        }

        return new HomepageData($saleResults, $exchangeResults, $giveawayResults, []);
    }
}