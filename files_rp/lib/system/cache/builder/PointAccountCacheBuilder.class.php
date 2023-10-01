<?php

namespace rp\system\cache\builder;

use rp\data\point\account\PointAccount;
use rp\data\raid\event\RaidEventList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 *  Project:    Raidplaner: Core
 *  Package:    dev.daries.rp
 *  Link:       http://daries.dev
 *
 *  Copyright (C) 2018-2023 Daries.dev Developer Team
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
 * Caches the point accounts.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Cache\Builder
 */
class PointAccountCacheBuilder extends AbstractCacheBuilder
{

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [
            'accounts' => [],
            'raidEvents' => []
        ];

        // get point accounts
        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_point_account
                WHERE   gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$parameters['gameID']]);

        /** @var PointAccount $object */
        while ($object = $statement->fetchObject(PointAccount::class)) {
            $data['accounts'][$object->pointAccountID] = $object;

            $raidEventList = new RaidEventList();
            $raidEventList->getConditionBuilder()->add('gameID = ?', [$parameters['gameID']]);
            $raidEventList->getConditionBuilder()->add('pointAccountID = ?', [$object->pointAccountID]);
            $raidEventList->readObjects();
            foreach ($raidEventList as $raidEvent) {
                if (!isset($data['raidEvents'][$object->pointAccountID])) $data['raidEvents'][$object->pointAccountID] = [];
                $data['raidEvents'][$object->pointAccountID][$raidEvent->eventID] = $raidEvent;
            }
        }

        return $data;
    }
}
