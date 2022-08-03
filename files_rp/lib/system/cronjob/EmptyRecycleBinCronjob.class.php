<?php

namespace rp\system\cronjob;

use rp\data\event\EventAction;
use wcf\data\cronjob\Cronjob;
use wcf\system\cronjob\AbstractCronjob;
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
 * Deletes thrashed calendar events.
 * 
 * @author      Marco Daries
 * @package     Daries\RP
 */
class EmptyRecycleBinCronjob extends AbstractCronjob
{

    /**
     * @inheritDoc
     */
    public function execute(Cronjob $cronjob)
    {
        parent::execute($cronjob);

        if (RP_EVENT_EMPTY_RECYCLE_BIN_CYCLE) {
            $sql = "SELECT	eventID
                    FROM	rp" . WCF_N . "_event
                    WHERE	isDeleted = ?
                        AND deleteTime < ?";
            $statement = WCF::getDB()->prepareStatement($sql, 1000);
            $statement->execute([
                1,
                TIME_NOW - RP_EVENT_EMPTY_RECYCLE_BIN_CYCLE * 86400,
            ]);
            $eventIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);

            if (!empty($eventIDs)) {
                $action = new EventAction($eventIDs, 'delete');
                $action->executeAction();
            }
        }
    }
}
