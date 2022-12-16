<?php

namespace GroupThr3e\AJExchange\Util;

use GroupThr3e\AJExchange\Constants\ApprovalStatus;
use GroupThr3e\AJExchange\Constants\ListingType;
use GroupThr3e\AJExchange\Models\HomepageData;
use GroupThr3e\AJExchange\Models\Listing;

class HomeDataset extends DatasetBase
{
    public function getHomepage(): HomepageData
    {
        $approved = ApprovalStatus::APPROVED;
        $ownId = Auth::getAuthManager()->getUser()->getUserId();

        $listingQuery =
            "SELECT Listing.*, filename as imageUrls FROM Listing
             LEFT JOIN ListingImage ON Listing.listingId = ListingImage.listingId
             WHERE type = :type 
             AND approvalStatus = '$approved'
             AND orderId IS NULL
             AND imageIndex = 1
             AND userId != $ownId
             LIMIT 4";

        $statement = $this->dbHandle->prepare($listingQuery);

        $statement->execute(['type' => ListingType::SALE]);
        $saleResults = [];
        foreach ($statement->fetchAll() as $result) {
            $saleResults[] = new Listing($result);
        }

        $statement->execute(['type' => ListingType::EXCHANGE]);
        $exchangeResults = [];
        foreach ($statement->fetchAll() as $result) {
            $exchangeResults[] = new Listing($result);
        }

        $statement->execute(['type' => ListingType::GIVEAWAY]);
        $giveawayResults = [];
        foreach ($statement->fetchAll() as $result) {
            $giveawayResults[] = new Listing($result);
        }

        $tagDataset = new TagDataset();
        $tags = $tagDataset->getTags();

        return new HomepageData($saleResults, $exchangeResults, $giveawayResults, $tags);
    }
}