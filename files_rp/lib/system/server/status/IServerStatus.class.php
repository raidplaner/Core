<?php

namespace rp\system\server\status;

use rp\data\server\Server;

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
 * Any game server status should implement this interface.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Server\Status
 */
interface IServerStatus
{

    /**
     *  Returns the content of this server status.
     */
    public function getContent(): string;

    /**
     * Return the selected server
     */
    public function getServer(): Server;

    /**
     * Set the selected server
     */
    public function setServer(Server $server): void;
}
