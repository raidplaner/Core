<?php

namespace rp\system\page\handler;

use wcf\system\page\handler\AbstractMenuPageHandler;
use wcf\system\WCF;


/**
 * Menu page handler for the list of participations.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Page\Handler
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
