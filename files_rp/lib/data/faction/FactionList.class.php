<?php

namespace rp\data\faction;

use wcf\data\DatabaseObjectList;


/**
 * Represents a list of factions.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      Faction         current()
 * @method      Faction[]       getObjects()
 * @method      Faction|null    search($objectID)
 * @property    Faction[]       $objects
 */
class FactionList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Faction::class;

}
