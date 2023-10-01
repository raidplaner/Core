<?php

namespace rp\system\cache\runtime;

use rp\data\event\Event;
use rp\data\event\EventList;
use wcf\system\cache\runtime\AbstractRuntimeCache;


/**
 * Runtime cache implementation for events.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method      Event[]     getCachedObjects()
 * @method      Event       getObject($objectID)
 * @method      Event[]     getObjects(array $objectIDs)
 */
class EventRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = EventList::class;

}
