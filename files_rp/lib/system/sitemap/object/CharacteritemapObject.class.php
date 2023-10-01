<?php

namespace rp\system\sitemap\object;

use rp\data\character\Character;
use wcf\data\page\PageCache;
use wcf\system\sitemap\object\AbstractSitemapObjectObjectType;
use wcf\system\WCF;


/**
 * Character sitemap implementation.
 *
 * @author  Marco Daries
 * @package     Daries\RP\Sitemap\Object
 */
class CharacteritemapObject extends AbstractSitemapObjectObjectType
{

    /**
     * @inheritDoc
     */
    public function getObjectClass()
    {
        return Character::class;
    }

    /**
     * @inheritDoc
     */
    public function getLastModifiedColumn()
    {
        return 'lastUpdateTime';
    }

    /**
     * @inheritDoc
     */
    public function isAvailableType()
    {
        if (!WCF::getSession()->getPermission('user.rp.canViewCharacterProfile')) {
            return false;
        }

        return PageCache::getInstance()->getPageByIdentifier('dev.daries.rp.Character')->allowSpidersToIndex;
    }
}
