<?php

namespace GroupThr3e\AJExchange\Util;

use GroupThr3e\AJExchange\Constants\ApprovalStatus;
use GroupThr3e\AJExchange\Models\Listing;

class PurchaseDataset extends DatasetBase
{
    /**
     * Makes a purchase of the given listing
     * @param int $listingId the listing to purchase
     * @return int|string the id of the purchase if successful, otherwise returns a string containing the error
     */
    public function makePurchase(int $listingId): int|string
    {
        $listingStatement = $this->dbHandle->prepare('SELECT * FROM Listing WHERE listingId = :listingId');
        $listingStatement->execute(['listingId' => $listingId]);
        $listingResult = $listingStatement->fetch();
        if ($listingResult === false) return 'Listing not found';

        $listing = new Listing($listingResult);
        $currentId = Auth::getAuthManager()->getUser()->getUserId();
        if ($listing->getApprovalStatus() !== ApprovalStatus::APPROVED) return 'Listing not found';
        if ($listing->getPurchaseId() !== null) return 'Listing already purchased';
        if ($listing->getUserId() === $currentId) return 'Cannot purchase own listing';

        $purchaseQuery = 'INSERT INTO Purchase (userId, listingId) VALUE (:currentId, :listingId)';
        $purchaseStatement = $this->dbHandle->prepare($purchaseQuery);
        $purchaseStatement->execute(['currentId' => $currentId, 'listingId' => $listingId]);
        $purchaseId = (int)$this->dbHandle->lastInsertId();

        $updateListing = $this->dbHandle->prepare('UPDATE Listing SET purchaseId = :purchaseId WHERE listingId = :listingId');
        $updateListing->execute(['purchaseId' => $this->dbHandle->lastInsertId(), 'listingId' => $listingId]);
        return $purchaseId;
    }
}