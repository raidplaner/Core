<?php

namespace rp\data\raid;

use wcf\data\DatabaseObjectList;

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
 * @author      Marco Daries
 * @package     Daries\RP\Data\Raid
 *
 * @method      Raid        current()
 * @method      Raid[]      getObjects()
 * @method      Raid|null   search($objectID)
 * @property	Raid[]      $objects
 */
class RaidList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Raid::class;

    /**
     * Returns timestamp of oldest raid fetched.
     */
    public function getLastRaidTime(): int
    {
        $lastRaidTime = 0;
        foreach ($this->objects as $raid) {
            if (!$lastRaidTime) {
                $lastRaidTime = $raid->date;
            }

            $lastRaidTime = \min($lastRaidTime, $raid->date);
        }

        return $lastRaidTime;
    }

    /**
     * Truncates the items in object list to given number of items.
     */
    public function truncate(int $limit): void
    {
        $this->objects = \array_slice($this->objects, 0, $limit, true);
        $this->indexToObject = \array_keys($this->objects);
    }
}
