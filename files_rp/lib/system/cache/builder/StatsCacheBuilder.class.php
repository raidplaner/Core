<?php

namespace rp\system\cache\builder;

use wcf\system\cache\builder\AbstractCacheBuilder;
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
 * Caches the number of events and the events per day.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Cache\Builder
 */
class StatsCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected $maxLifetime = 1200;

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters)
    {
        $data = [];

        // number of characters
        $sql = "SELECT  COUNT(*)
                FROM    rp" . WCF_N . "_member
                WHERE   isDisabled = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([0]);
        $data['characters'] = $statement->fetchSingleColumn();

        // number of comments
        $sql = "SELECT  SUM(comments)
                FROM    rp" . WCF_N . "_event
                WHERE   comments > 0";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        $data['comments'] = $statement->fetchSingleColumn();

        // number of events
        $sql = "SELECT  COUNT(*)
                FROM    rp" . WCF_N . "_event";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        $data['events'] = $statement->fetchSingleColumn();

        // number of raids
        $sql = "SELECT  COUNT(*)
                FROM    rp" . WCF_N . "_raid";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        $data['raids'] = $statement->fetchSingleColumn();

        // raids/events per Day
        $days = \ceil((TIME_NOW - RP_INSTALL_DATE) / 86400);
        if ($days <= 0) {
            $days = 1;
        }
        $data['eventsPerDay'] = $data['events'] / $days;
        $data['raidsPerDay'] = $data['raids'] / $days;

        return $data;
    }
}
