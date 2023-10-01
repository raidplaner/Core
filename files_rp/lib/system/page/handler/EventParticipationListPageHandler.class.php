<?php

namespace rp\system\page\handler;

use wcf\system\page\handler\AbstractMenuPageHandler;
use wcf\system\WCF;


/**
 * Menu page handler for the list of participations.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class EventParticipationListPageHandler extends AbstractMenuPageHandler
{

    /**
     * @inheritDoc
     */
    public function isVisible($objectID = null)
    {
        return WCF::getUser()->userID;
    }
}
