<?php

namespace rp\system\cache\runtime;

use rp\data\character\CharacterList;
use rp\data\character\CharacterProfile;
use wcf\system\cache\runtime\AbstractRuntimeCache;


/**
 * Runtime cache implementation for characters.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Cache\Runtime
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
