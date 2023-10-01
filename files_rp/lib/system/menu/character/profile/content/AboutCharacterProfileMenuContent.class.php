<?php

namespace rp\system\menu\character\profile\content;

use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use wcf\system\event\EventHandler;
use wcf\system\SingletonFactory;
use wcf\system\WCF;


/**
 * Handles character profile about content.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class AboutCharacterProfileMenuContent extends SingletonFactory implements ICharacterProfileMenuContent
{

    /**
     * @inheritDoc
     */
    public function getContent(int $characterID): string
    {
        $character = CharacterProfileRuntimeCache::getInstance()->getObject($characterID);
        
        EventHandler::getInstance()->fireAction($this, 'getContent');

        WCF::getTPL()->assign([
            'notes' => !empty($character->notes) ? $character->getFormattedNotes() : '',
        ]);

        return WCF::getTPL()->fetch('characterProfileAbout', 'rp');
    }

    /**
     * @inheritDoc
     */
    public function isVisible(int $characterID): bool
    {
        return true;
    }
}
