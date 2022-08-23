<?php

namespace rp\system\sitemap\object;

use rp\data\event\Event;
use wcf\data\DatabaseObject;
use wcf\data\page\PageCache;
use wcf\system\sitemap\object\AbstractSitemapObjectObjectType;

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
 * Event sitemap implementation.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Sitemap\Object
 */
class EventSitemapObject extends AbstractSitemapObjectObjectType
{

    /**
     * @inheritDoc
     */
    public function canView(DatabaseObject $object): bool
    {
        /** @var Event $object */
        return $object->canRead();
    }

    /**
     * @inheritDoc
     */
    public function getLastModifiedColumn(): string
    {
        return 'created';
    }

    /**
     * @inheritDoc
     */
    public function getObjectClass(): string
    {
        return Event::class;
    }

    /**
     * @inheritDoc
     */
    public function isAvailableType(): int
    {
        return PageCache::getInstance()->getPageByIdentifier('info.daries.rp.Event')->allowSpidersToIndex;
    }
}
