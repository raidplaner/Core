<?php

namespace rp\system\page\handler;

use rp\system\cache\runtime\CharacterRuntimeCache;
use wcf\system\page\handler\AbstractLookupPageHandler;
use wcf\system\page\handler\IOnlineLocationPageHandler;

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
 * Menu page handler for the character profile page.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Page\Handler
 */
class CharacterPageHandler extends AbstractLookupPageHandler implements IOnlineLocationPageHandler
{
    use TCharacterLookupPageHandler;
    use TCharacterOnlineLocationPageHandler;

    /**
     * @inheritDoc
     */
    public function getLink($objectID)
    {
        return CharacterRuntimeCache::getInstance()->getObject($objectID)->getLink();
    }
}
