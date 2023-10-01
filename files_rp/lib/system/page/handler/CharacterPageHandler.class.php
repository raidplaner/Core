<?php

namespace rp\system\page\handler;

use rp\system\cache\runtime\CharacterRuntimeCache;
use wcf\system\page\handler\AbstractLookupPageHandler;
use wcf\system\page\handler\IOnlineLocationPageHandler;


/**
 * Menu page handler for the character profile page.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class CharacterPageHandler extends AbstractLookupPageHandler implements IOnlineLocationPageHandler
{
    use TCharacterLookupPageHandler;
    use TCharacterOnlineLocationPageHandler;

    /**
     * @inheritDoc
     */
    public function getLink($objectID)
    {
        return CharacterRuntimeCache::getInstance()->getObject($objectID)->getLink();
    }
}
