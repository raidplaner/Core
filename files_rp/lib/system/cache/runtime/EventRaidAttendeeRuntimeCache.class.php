<?php

namespace rp\system\cache\runtime;

use rp\data\event\raid\attendee\EventRaidAttendee;
use rp\data\event\raid\attendee\EventRaidAttendeeList;
use wcf\system\cache\runtime\AbstractRuntimeCache;


/**
 * Runtime cache implementation for event raid attendees.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Cache\Runtime
 * 
 * @method      EventRaidAttendee[]     getCachedObjects()
 * @method      EventRaidAttendee       getObject($objectID)
 * @method      EventRaidAttendee[]     getObjects(array $objectIDs)
 */
class EventRaidAttendeeRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = EventRaidAttendeeList::class;

}
