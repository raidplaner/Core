<?php

namespace rp\data\event;

use wcf\data\DatabaseObjectList;


/**
 * Represents a list of events.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      Event           current()
 * @method      Event[]         getObjects()
 * @method      Event|null      search($objectID)
 * @property    Event[]         $objects
 */
class EventList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Event::class;

    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'event.startTime';

}
