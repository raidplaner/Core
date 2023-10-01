<?php

namespace rp\data\classification;

use wcf\data\DatabaseObjectList;


/**
 * Represents a list of classifications.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      Classification          current()
 * @method      Classification[]        getObjects()
 * @method      Classification|null     search($objectID)
 * @property    Classification[]        $objects
 */
class ClassificationList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Classification::class;

}
