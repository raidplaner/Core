<?php

namespace rp\system\cache\runtime;

use rp\data\raid\Raid;
use rp\data\raid\RaidList;
use wcf\system\cache\runtime\AbstractRuntimeCache;


/**
 * @author  Marco Daries
 * @package     Daries\RP\System\Cache\Runtime
 * 
 * @method      Raid[]      getCachedObjects()
 * @method      Raid        getObject($objectID)
 * @method      Raid[]      getObjects(array $objectIDs)
 */
class RaidRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = RaidList::class;

}
