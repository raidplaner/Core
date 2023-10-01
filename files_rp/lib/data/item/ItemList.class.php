<?php

namespace rp\data\item;

use wcf\data\DatabaseObjectList;


/**
 * Represents a list of items.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      Item        current()
 * @method      Item[]      getObjects()
 * @method      Item|null   search($objectID)
 * @property	Item[]      $objects
 */
class ItemList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Item::class;

}
