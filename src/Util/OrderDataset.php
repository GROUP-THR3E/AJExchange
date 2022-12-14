<?php

namespace GroupThr3e\AJExchange\Util;

use GroupThr3e\AJExchange\Constants\ApprovalStatus;
use GroupThr3e\AJExchange\Models\Listing;

class OrderDataset extends DatasetBase
{
    /**
     * Makes an order of the given listing
     * @param int $listingId the listing to order
     * @return int|string the id of the order if successful, otherwise returns a string containing the error
     */
    public function makeOrder(int $listingId): int|string
    {
        $listingStatement = $this->dbHandle->prepare('SELECT * FROM Listing WHERE listingId = :listingId');
        $listingStatement->execute(['listingId' => $listingId]);
        $listingResult = $listingStatement->fetch();
        if ($listingResult === false) return 'Listing not found';

        $listing = new Listing($listingResult);
        $currentId = Auth::getAuthManager()->getUser()->getUserId();
        if ($listing->getApprovalStatus() !== ApprovalStatus::APPROVED) return 'Listing not found';
        if ($listing->getOrderId() !== null) return 'Listing already ordered';
        if ($listing->getUserId() === $currentId) return 'Cannot ordered own listing';

        $orderQuery = 'INSERT INTO `Order` (userId, listingId) VALUE (:currentId, :listingId)';
        $orderStatement = $this->dbHandle->prepare($orderQuery);
        $orderStatement->execute(['currentId' => $currentId, 'listingId' => $listingId]);
        $orderId = (int)$this->dbHandle->lastInsertId();

        $updateListing = $this->dbHandle->prepare('UPDATE Listing SET orderId = :orderId WHERE listingId = :listingId');
        $updateListing->execute(['orderId' => $this->dbHandle->lastInsertId(), 'listingId' => $listingId]);
        return $orderId;
    }
}