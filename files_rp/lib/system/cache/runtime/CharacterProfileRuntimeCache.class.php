<?php

namespace rp\system\cache\runtime;

use rp\data\character\CharacterProfile;
use rp\data\character\CharacterProfileList;
use wcf\system\cache\runtime\AbstractRuntimeCache;


/**
 * Runtime cache implementation for character profiles.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method      CharacterProfile[]      getCachedObjects()
 * @method      CharacterProfile        getObject($objectID)
 * @method      CharacterProfile[]      getObjects(array $objectIDs)
 */
class CharacterProfileRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = CharacterProfileList::class;

    /**
     * Adds a character profile to the cache. This is an internal method that should
     * not be used on a regular basis.
     */
    public function addCharacterProfile(CharacterProfile $profile): void
    {
        $objectID = $profile->getObjectID();

        if (!isset($this->objects[$objectID])) {
            $this->objects[$objectID] = $profile;
        }
    }
}
