<?php

namespace rp\system\page\handler;

use rp\data\event\ViewableEvent;
use wcf\system\page\handler\AbstractMenuPageHandler;


/**
 * Page menu handler for the page listing all unread events.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class UnreadEventListPageHandler extends AbstractMenuPageHandler
{

    /**
     * @inheritDoc
     */
    public function getOutstandingItemCount($objectID = null): int
    {
        return ViewableEvent::getUnreadEvents();
    }

    /**
     * @inheritDoc
     */
    public function isVisible($objectID = null): bool
    {
        return ViewableEvent::getUnreadEvents() > 0;
    }
}
