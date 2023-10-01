<?php

namespace rp\data\event;

use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;


/**
 * Represents a list of unread events.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class UnreadEventList extends AccessibleEventList
{

    public function __construct()
    {
        parent::__construct();

        $this->sqlConditionJoins .= " LEFT JOIN wcf" . WCF_N . "_tracked_visit tracked_visit ON (tracked_visit.objectTypeID = " . VisitTracker::getInstance()->getObjectTypeID('dev.daries.rp.event') . " AND tracked_visit.objectID = event.eventID AND tracked_visit.userID = " . WCF::getUser()->userID . ")";
        $this->getConditionBuilder()->add('event.created > ?', [VisitTracker::getInstance()->getVisitTime('dev.daries.rp.event')]);
        $this->getConditionBuilder()->add('(event.created > tracked_visit.visitTime OR tracked_visit.visitTime IS NULL)');
    }
}
