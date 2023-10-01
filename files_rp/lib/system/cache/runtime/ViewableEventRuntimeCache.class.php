<?php

namespace rp\system\cache\runtime;

use rp\data\event\ViewableEvent;
use rp\data\event\ViewableEventList;
use wcf\system\cache\runtime\AbstractRuntimeCache;


/**
 * Runtime cache implementation for viewable events.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Cache\Runtime
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
