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
 * @author      Marco Daries
 * @package     Daries\RP\System\Server\Status
 */
abstract class AbstractServerStatus implements IServerStatus
{
    /**
     * selected server
     */
    protected Server $server;

    /**
     * @inheritDoc
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @inheritDoc
     */
    public function setServer(Server $server): void
    {
        $this->server = $server;
    }
}
