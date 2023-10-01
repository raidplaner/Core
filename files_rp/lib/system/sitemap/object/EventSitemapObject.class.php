<?php

namespace rp\system\sitemap\object;

use rp\data\event\Event;
use wcf\data\DatabaseObject;
use wcf\data\page\PageCache;
use wcf\system\sitemap\object\AbstractSitemapObjectObjectType;


/**
 * Event sitemap implementation.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Sitemap\Object
 */
class EventSitemapObject extends AbstractSitemapObjectObjectType
{

    /**
     * @inheritDoc
     */
    public function canView(DatabaseObject $object): bool
    {
        /** @var Event $object */
        return $object->canRead();
    }

    /**
     * @inheritDoc
     */
    public function getLastModifiedColumn(): string
    {
        return 'created';
    }

    /**
     * @inheritDoc
     */
    public function getObjectClass(): string
    {
        return Event::class;
    }

    /**
     * @inheritDoc
     */
    public function isAvailableType(): int
    {
        return PageCache::getInstance()->getPageByIdentifier('dev.daries.rp.Event')->allowSpidersToIndex;
    }
}
