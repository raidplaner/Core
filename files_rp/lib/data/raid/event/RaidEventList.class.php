<?php

namespace rp\data\raid\event;

use wcf\data\DatabaseObjectList;


/**
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      RaidEvent           current()
 * @method      RaidEvent[]         getObjects()
 * @method      RaidEvent|null      search($objectID)
 * @property	RaidEvent[]         $objects
 */
class RaidEventList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = RaidEvent::class;

}
