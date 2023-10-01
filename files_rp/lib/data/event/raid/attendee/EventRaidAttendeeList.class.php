<?php

namespace rp\data\event\raid\attendee;

use wcf\data\DatabaseObjectList;


/**
 * Represents a list of event raid attendees.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      EventRaidAttendee           current()
 * @method      EventRaidAttendee[]         getObjects()
 * @method      EventRaidAttendee|null      search($objectID)
 * @property    EventRaidAttendee[]         $objects
 */
class EventRaidAttendeeList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = EventRaidAttendee::class;

}
