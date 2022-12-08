<?php

namespace GroupThr3e\AJExchange\Util;

use GroupThr3e\AJExchange\Models\Listing;

class ListingDataset extends DatasetBase
{
    /**
     * Retrieves a single listing by its ID
     * @param int $listingId the id of the listing to retrieve
     * @return Listing the retrieved listing
     */
    public function getListing(int $listingId): Listing
    {
        $query = '
            SELECT * FROM Listing 
            INNER JOIN User ON Listing.userId = User.userId
            INNER JOIN Office ON User.officeId = Office.officeId
            WHERE listingId = :listingId';

        $statement = $this->dbHandle->prepare($query);
        $statement->execute(['listingId' => $listingId]);
        $result = $statement->fetch();
        $listing = new Listing($result);
        $listing->setUser($result);
        $listing->getUser()->setOffice($result);
        return $listing;
    }

    /**
     * Retrieves a list of listings
     * @param int $limit the amount to retrieve
     * @param int $offset the offset to use
     * @return array the list of listings
     */
    public function getListings(int $limit = 20, int $offset = 0): array
    {
        $query = '
            SELECT * FROM Listing
            INNER JOIN User ON Listing.userId = User.userId
            INNER JOIN Office ON User.officeId = Office.officeId
            LIMIT :limit OFFSET :offset';

        $statement = $this->dbHandle->prepare($query);
        $statement->execute(['limit' => $limit, 'offset' => $offset]);

        $listings = [];
        foreach ($statement->fetchAll() as $result) {
            $listing = new Listing($result);
            $listing->setUser($result);
            $listing ->getUser()->setOffice($result);
            $listings[] = $listing;
        }
        return $listings;
    }

    public function searchListings(string $query = '', array $tags = [], ?string $type = null, ?int $officeId = null,
                                   ?int $userId = null, int $limit = 20, int $offset = 0): array
    {
        $sqlQuery = '
            SELECT * FROM Listing
            INNER JOIN User ON Listing.userId = User.userId
            INNER JOIN Office ON User.officeId = Office.officeId
            WHERE listingName LIKE :query';

        $sqlParams = ['query' => "%$query%"];
        if ($type !== null) {
            $sqlQuery .= ' AND type = :type';
            $sqlParams['type'] = $type;
        } if ($officeId !== null) {
            $sqlQuery .= ' AND officeId = :officeId';
            $sqlParams['officeId'] = $officeId;
        } if ($userId !== null) {
            $sqlQuery .= ' AND userId = :userId';
            $sqlParams['userId'] = $userId;
        }

        $sqlQuery .= ' LIMIT :limit OFFSET :offset';
        $sqlParams['limit'] = $limit;
        $sqlParams['offset'] = $offset;

        $statement = $this->dbHandle->prepare($sqlQuery);
        $statement->execute($sqlParams);

        $results = [];
        foreach ($statement->fetchAll() as $result) {
            $listing = new Listing($result);
            $listing->setUser($result);
            $listing->getUser()->setOffice($result);
            $results[] = $listing;
        }
        return $results;
    }
}