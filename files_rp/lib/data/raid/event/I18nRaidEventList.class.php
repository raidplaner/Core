<?php

namespace rp\data\raid\event;

use wcf\data\I18nDatabaseObjectList;

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
 * I18n implementation of raid event list.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Data\Raid\Event
 *
 * @method      RaidEvent           current()
 * @method      RaidEvent[]         getObjects()
 * @method      RaidEvent|null      getSingleObject()
 * @method      RaidEvent|null      search($objectID)
 * @property    RaidEvent[]         $objects
 */
class I18nRaidEventList extends I18nDatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $i18nFields = ['eventName' => 'eventNameI18n'];

    /**
     * @inheritDoc
     */
    public $className = RaidEvent::class;

}
