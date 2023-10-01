<?php

namespace rp\data\item\database;

use wcf\data\DatabaseObjectList;


/**
 * Represents a list of item databases.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      ItemDatabase        current()
 * @method      ItemDatabase[]      getObjects()
 * @method      ItemDatabase|null   getSingleObject()
 * @method      ItemDatabase|null   search($objectID)
 * @property    ItemDatabase[]      $objects
 */
class ItemDatabaseList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = ItemDatabase::class;

}
