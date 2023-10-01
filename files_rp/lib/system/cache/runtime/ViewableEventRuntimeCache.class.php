<?php

namespace rp\system\cache\runtime;

use rp\data\event\ViewableEvent;
use rp\data\event\ViewableEventList;
use wcf\system\cache\runtime\AbstractRuntimeCache;


/**
 * Runtime cache implementation for viewable events.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method      ViewableEvent[]     getCachedObjects()
 * @method      ViewableEvent       getObject($objectID)
 * @method      ViewableEvent[]     getObjects(array $objectIDs)
 */
class ViewableEventRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = ViewableEventList::class;

}
