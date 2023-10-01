<?php

namespace rp\system\page\handler;

use rp\data\event\ViewableEvent;
use wcf\system\page\handler\AbstractMenuPageHandler;
use wcf\system\WCF;


/**
 * Page handler implementation for the calendar.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class CalendarPageHandler extends AbstractMenuPageHandler
{

    /**
     * @inheritDoc
     */
    public function getOutstandingItemCount($objectID = null)
    {
        return ViewableEvent::getUnreadEvents();
    }

    /**
     * @inheritDoc
     */
    public function isVisible($objectID = null)
    {
        return WCF::getSession()->getPermission('user.rp.canReadEvent');
    }
}
