<?php

namespace GroupThr3e\AJExchange\Util;

use DateTime;
use GroupThr3e\AJExchange\Constants\ApprovalStatus;
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
        SELECT listingId, listingName, description, price, desiredItem, type, dateListed, approvalStatus, orderId, 
               charityId, charityName, userId, email, password, fullName, role, officeId, officeName, address, 
               COALESCE(GROUP_CONCAT(tag), \'\') as tags, GROUP_CONCAT(filename) as imageUrls
        FROM (
            SELECT Listing.*, email, password, fullName, role, User.officeId, officeName, address, tag, filename, charityName FROM Listing
            INNER JOIN User ON Listing.userId = User.userId
            INNER JOIN Office ON User.officeId = Office.officeId
            LEFT JOIN Charity ON Listing.charityId = Charity.charityId
            LEFT JOIN ListingTag ON Listing.listingId = ListingTag.listingId 
            LEFT JOIN ListingImage ON Listing.listingId = ListingImage.listingId
            WHERE Listing.listingId = :listingId
            ORDER BY Listing.listingId, ListingImage.imageIndex
        ) as Results
        GROUP BY listingId;
        ';

        $statement = $this->dbHandle->prepare($query);
        $statement->execute(['listingId' => $listingId]);
        $result = $statement->fetch();
        return new Listing($result);
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
                                   ?int $userId = null, ?string $approvalStatus = null, bool $hideOwnListings = false,
                                   bool $showOrdered = true, int $limit = 1000000000, int $offset = 0): array
    {
        $filterTags = sizeof($tags) > 0;
        $tagFilterFunc = 'FIND_IN_SET(:tag0, tags) ';
        for ($i = 1; $i < sizeof($tags); $i++) {
            $tagFilterFunc .= "AND FIND_IN_SET(:tag$i, tags) ";
        }

        $tagSelect = $filterTags ? ',tag': '';
        $tagGroup = $filterTags ? ', GROUP_CONCAT(tag) as tags': '';
        $tagQuery = $filterTags ? 'INNER JOIN ListingTag ON Listing.listingId = ListingTag.listingId': '';
        $hideOwnListingsFilter =  $hideOwnListings ? 'AND Listing.userId <> :ownUserId' : '';
        $typeFilter = $type === null ? '' : 'AND type = :type';
        $officeFilter = $officeId === null ? '' : 'AND User.officeId = :officeId';
        $userFilter = $userId === null ? '' : 'AND Listing.userId = :userId';
        $approvalFilter = $approvalStatus === null ? '' : 'AND approvalStatus = :approvalStatus';
        $showOrderedFilter = $showOrdered ? '' : 'AND orderId IS NULL';
        $tagFilter = $filterTags ? "HAVING $tagFilterFunc": '';

        $sqlQuery =
            "SELECT listingId, listingName, description, price, desiredItem, type, dateListed, approvalStatus, 
                    orderId, charityId, charityName, userId, email, password, fullName, role, officeId, officeName, 
                    address, GROUP_CONCAT(filename) as imageUrls $tagGroup
            FROM (
                SELECT Listing.*, email, password, fullName, role, User.officeId, officeName, address, filename, 
                       charityName $tagSelect FROM Listing
                INNER JOIN User ON Listing.userId = User.userId
                INNER JOIN Office ON User.officeId = Office.officeId
                LEFT JOIN Charity ON Listing.charityId = Charity.charityId 
                LEFT JOIN ListingImage ON Listing.listingId = ListingImage.listingId
                $tagQuery
                WHERE listingName LIKE :query
                AND imageIndex = 1
                $hideOwnListingsFilter $typeFilter $officeFilter $userFilter $approvalFilter $showOrderedFilter 
                ORDER BY Listing.listingId, ListingImage.imageIndex
            ) as Results
            GROUP BY listingId
            $tagFilter
            LIMIT :limit OFFSET :offset;
            ";



        $sqlParams = ['query' => "%$query%"];
        if ($hideOwnListings) {
            $sqlParams['ownUserId'] = Auth::getAuthManager()->getUser()->getUserId();
        } if ($type !== null) {
            $sqlParams['type'] = $type;
        } if ($officeId !== null) {
            $sqlParams['officeId'] = $officeId;
        } if ($userId !== null) {
            $sqlParams['userId'] = $userId;
        } if ($approvalStatus !== null) {
            $sqlParams['approvalStatus'] = $approvalStatus;
        } if ($filterTags) {
            for ($i = 0; $i < sizeof($tags); $i++)
                $sqlParams["tag$i"] = $tags[$i];
        }

        $sqlParams['limit'] = $limit;
        $sqlParams['offset'] = $offset;

        $statement = $this->dbHandle->prepare($sqlQuery);
        $statement->execute($sqlParams);

        $results = [];
        foreach ($statement->fetchAll() as $result) {
            $results[] = new Listing($result);
        }
        return $results;
    }

    /**
     * Adds a new listing to the database and moves the attached files to the images folder.
     * @param string $name the name of the new listing
     * @param string $description the description of the listing
     * @param float|null $price the price of the item
     * @param string|null $desiredItem the item th eu
     * @param string $type the type of the listing (sale,exchange,giving away)
     * @param array $tags the tags of the listing
     * @param int $userId the id of the user listing the item
     * @return bool return true of the creation was successful
     */
    public function createListing(string $name, string $description, ?float $price, ?string $desiredItem, string $type,
                                  array $tags, int $userId, array $images, ?int $charityId): bool
    {
        $query = 'INSERT INTO Listing (listingName, description, price, desiredItem, type, dateListed, approvalStatus, userId, orderId, charityId)
                  VALUES (:listingName, :description, :price, :desiredItem, :type, :dateListed, :approvalStatus, :userId, NULL, :charityId)';
        $insertListing = $this->dbHandle->prepare($query);
        $insertListing->execute([
            'listingName' => $name,
            'description' => $description,
            'price' => $price,
            'desiredItem' => $desiredItem,
            'type' => $type,
            'dateListed' => (new DateTime())->format('Y-m-d H:i:s'),
            'approvalStatus' => ApprovalStatus::PENDING,
            'userId' => $userId,
            'charityId' => $charityId
        ]);

        $listingId = $this->dbHandle->lastInsertId();
        $insertImage = $this->dbHandle->prepare('INSERT INTO ListingImage (filename, imageIndex, listingId) VALUES (:filename, :imageIndex, :listingId)');
        $imageIndex = 1;
        foreach ($images as $image) {
            // Skips if upload contains errors
            if ($image->getError() !== 0) continue;

            // Randomly generates a filename, checking if it already exists
            $extension = pathinfo($image->getClientFilename(), PATHINFO_EXTENSION);
            do {
                $filename = bin2hex(random_bytes(7)) . '.' . $extension;
                $path = 'public/images/' . $filename;

            } while(file_exists($path));
            $image->moveTo($path);
            $insertImage->execute(['filename' => $filename, 'imageIndex' => $imageIndex, 'listingId' => $listingId]);
            $imageIndex++;
        }

        $tagDataset = new TagDataset();
        $tagDataset->addListingTags($listingId, $tags);

        return $listingId;
    }

    /**
     * Sets the approval status of the listing of the given id
     * @param int $listingId the id of the listing to update
     * @param string $approvalStatus the approval status to set the listing to
     */
    public function setApproval(int $listingId, string $approvalStatus)
    {
        // Updates the approval status of the query
        $query = 'UPDATE Listing SET approvalStatus = :approvalStatus WHERE listingId = :listingId';
        $statement = $this->dbHandle->prepare($query);
        $statement->execute(['approvalStatus' => $approvalStatus, 'listingId' => $listingId]);

        // If it is being approved increase the listing count of each of the tags
        if ($approvalStatus === ApprovalStatus::APPROVED) {
            $tagsStatement = $this->dbHandle->prepare('SELECT tag FROM ListingTag WHERE listingId = :listingId');
            $tagsStatement->execute(['listingId' => $listingId]);
            $tags = [];
            foreach ($tagsStatement->fetchAll() as $result)
                $tags[] = $result['tag'];

            $tagDataset = new TagDataset();
            $tagDataset->increaseCounts($tags);
        }
    }
}