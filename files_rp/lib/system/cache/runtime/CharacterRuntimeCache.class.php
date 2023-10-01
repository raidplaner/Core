<?php

namespace rp\system\cache\runtime;

use rp\data\character\CharacterList;
use rp\data\character\CharacterProfile;
use wcf\system\cache\runtime\AbstractRuntimeCache;


/**
 * Runtime cache implementation for characters.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method      CharacterProfile[]      getCachedObjects()
 * @method      CharacterProfile        getObject($objectID)
 * @method      CharacterProfile[]      getObjects(array $objectIDs)
 */
class CharacterRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = CharacterList::class;

}
