<?php

namespace GroupThr3e\AJExchange\Util;

use DateTime;
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

    /**
     * Searches all listings with the specifies parameters
     * @param string $query the query to search listing titles with
     * @param array $tags the tags to search with (currently not implemented)
     * @param string|null $type the type to search for
     * @param int|null $officeId the id of the office to show results from
     * @param int|null $userId the id of the user
     * @param int $limit the amount of results to show
     * @param int $offset the offset to use
     * @return array the search results
     */
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

    /**
     * Adds a new listing to the database
     * @param string $name the name of the new listing
     * @param string $description the description of the listing
     * @param string $type the type of the listing (sale,exchange,giving away)
     * @param array $tags the tags of the listing
     * @param int $userId the id of the user listing the item
     * @return bool return true of the creation was successful
     */
    public function createListing(string $name, string $description, int $price, int $desiredItem, string $type, array $tags, int $userId): bool
    {
        $query = 'INSERT INTO Listing (listingName, description, price, desiredItem, type, dateListed, userId)
                  VALUES (:listingName, :description, :price, :desiredItem, :type, :dateListed, :userId)';
        $statement = $this->dbHandle->prepare($query);
        return $statement->execute([
            'listingName' => $name,
            'description' => $description,
            'price' => $price,
            'desiredItem' => $desiredItem,
            'type' => $type,
            'dateListed' => (new DateTime())->format('Y-m-d H:i:s'),
            'userId' => $userId
        ]);
    }
}