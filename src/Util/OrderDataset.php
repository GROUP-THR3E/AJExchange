<?php

namespace GroupThr3e\AJExchange\Util;

use GroupThr3e\AJExchange\Constants\ApprovalStatus;
use GroupThr3e\AJExchange\Models\Listing;
use GroupThr3e\AJExchange\Models\Order;

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

        $orderQuery = 'INSERT INTO `Order` (userId, listingId, orderDate) VALUE (:currentId, :listingId, NOW())';
        $orderStatement = $this->dbHandle->prepare($orderQuery);
        $orderStatement->execute(['currentId' => $currentId, 'listingId' => $listingId]);
        $orderId = (int)$this->dbHandle->lastInsertId();

        $updateListing = $this->dbHandle->prepare('UPDATE Listing SET orderId = :orderId WHERE listingId = :listingId');
        $updateListing->execute(['orderId' => $this->dbHandle->lastInsertId(), 'listingId' => $listingId]);
        return $orderId;
    }

    public function getUserOrders(int $userId): array
    {
        $query =
            'SELECT `Order`.*, Listing.*, User.*, Office.*, filename as imageUrls FROM `Order`
             INNER JOIN Listing ON `Order`.listingId = Listing.listingId
             INNER JOIN User ON Listing.userId = User.userId
             INNER JOIN Office ON User.officeId = Office.officeId
             INNER JOIN ListingImage ON Listing.listingId = ListingImage.listingId
             WHERE `Order`.userId = :userId
             AND imageIndex = 1;';

        $statement = $this->dbHandle->prepare($query);
        $statement->execute(['userId' => $userId]);

        $results = [];
        foreach ($statement->fetchAll() as $order)
            $results[] = new Order($order);

        return $results;
    }
}