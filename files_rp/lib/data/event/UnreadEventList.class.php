<?php

namespace rp\data\event;

use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;

/**
 *  Project:    Raidplaner: Core
 *  Package:    info.daries.rp
 *  Link:       http://daries.info
 *
 *  Copyright (C) 2018-2022 Daries.info Developer Team
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Represents a list of unread events.
 *
 * @author      Marco Daries
 * @package     Daries\RP
 */
class UnreadEventList extends AccessibleEventList
{

    public function __construct()
    {
        parent::__construct();

        $this->sqlConditionJoins .= " LEFT JOIN wcf" . WCF_N . "_tracked_visit tracked_visit ON (tracked_visit.objectTypeID = " . VisitTracker::getInstance()->getObjectTypeID('info.daries.rp.event') . " AND tracked_visit.objectID = event.eventID AND tracked_visit.userID = " . WCF::getUser()->userID . ")";
        $this->getConditionBuilder()->add('event.created > ?', [VisitTracker::getInstance()->getVisitTime('info.daries.rp.event')]);
        $this->getConditionBuilder()->add('(event.created > tracked_visit.visitTime OR tracked_visit.visitTime IS NULL)');
    }
}
