<?php

namespace rp\data\raid\event;

use rp\system\cache\builder\PointAccountCacheBuilder;
use rp\system\cache\builder\RaidEventCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;

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
 * Provides functions to edit raid event.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Raid\Event
 * 
 * @method static   RaidEvent       create(array $parameters = [])
 * @method          RaidEvent       getDecoratedObject()
 * @mixin           RaidEvent
 */
class RaidEventEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = RaidEvent::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        RaidEventCacheBuilder::getInstance()->reset();
        PointAccountCacheBuilder::getInstance()->reset();
    }
}
