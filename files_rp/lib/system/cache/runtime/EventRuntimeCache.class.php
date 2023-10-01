<?php

namespace rp\system\cache\runtime;

use rp\data\event\Event;
use rp\data\event\EventList;
use wcf\system\cache\runtime\AbstractRuntimeCache;

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
 * Runtime cache implementation for events.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Cache\Runtime
 * 
 * @method      Event[]     getCachedObjects()
 * @method      Event       getObject($objectID)
 * @method      Event[]     getObjects(array $objectIDs)
 */
class EventRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = EventList::class;

}
