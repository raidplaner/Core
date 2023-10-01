<?php

namespace rp\system\cache\runtime;

use rp\data\raid\Raid;
use rp\data\raid\RaidList;
use wcf\system\cache\runtime\AbstractRuntimeCache;


/**
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
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
