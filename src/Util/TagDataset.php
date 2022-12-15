<?php

namespace GroupThr3e\AJExchange\Util;

use GroupThr3e\AJExchange\Models\Tag;

class TagDataset extends DatasetBase
{
    /**
     * @return Tag[] a list of all tags in the database
     */
    public function getTags(): array
    {
        $statement = $this->dbHandle->query('SELECT * FROM Tag');
        $tags = [];
        foreach ($statement->fetchAll() as $result) {
            $tags = new Tag($result);
        }

        return $tags;
    }

    /**
     * Registers listing tags and creates any tag that don't already
     * @param array $tags the tags to add
     */
    public function addListingTags(int $listingId, array $tags)
    {
        // Prepares string for IN condition
        $tagsQuery = 'tag IN (';
        $sqlParams = [];
        for ($i = 0; $i < sizeof($tags); $i++) {
            $tagsQuery .= ":tag$i,";
            $sqlParams["tag$i"] = $tags[$i];
        }
        $tagsQuery = rtrim($tagsQuery, ',');
        $tagsQuery .= ')';

        // Retrieves all tags from the db that are in the given array
        $statement = $this->dbHandle->prepare("SELECT * FROM Tag WHERE $tagsQuery");
        $statement->execute($sqlParams);

        // Creates a list of missing tags to add
        $tagResults = [];
        foreach ($statement->fetchAll() as $result)
            $tagResults[] = $result['tag'];

        $missingTags = [];
        foreach ($tags as $tag)
            if (!in_array($tag, $tagResults))
                $missingTags[] = $tag;

        // Adds missing tags to db
        $insertStatement = $this->dbHandle->prepare('INSERT INTO Tag (tag, taggedListings) VALUES (:tag, 0)');
        foreach ($missingTags as $missingTag)
            $insertStatement->execute(['tag' => $missingTag]);

        // Adds all listings tags
        $insertListingTag = $this->dbHandle->prepare('INSERT INTO ListingTag (tag, listingId) VALUES (:tag, :listingId)');
        foreach ($tags as $tag)
            $insertListingTag->execute(['tag' => $tag, 'listingId' => $listingId]);
    }
}