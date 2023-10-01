<?php

namespace rp\data\race;

use wcf\data\DatabaseObjectList;


/**
 * Represents a list of races.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      Race        current()
 * @method      Race[]      getObjects()
 * @method      Race|null   search($objectID)
 * @property    Race[]      $objects
 */
class RaceList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Race::class;

}
