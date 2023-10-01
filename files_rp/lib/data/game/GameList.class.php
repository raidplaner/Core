<?php

namespace rp\data\game;

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
 * Represents a list of games.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Game
 *
 * @method      Game        current()
 * @method      Game[]      getObjects()
 * @method      Game|null   search($objectID)
 * @property    Game[]      $objects
 */
class GameList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Game::class;

}
