<?php

namespace rp\system\box;

use rp\data\server\ServerCache;
use rp\system\server\status\IServerStatus;
use wcf\data\box\Box;
use wcf\system\box\AbstractBoxController;
use wcf\system\box\IConditionBoxController;
use wcf\system\exception\ImplementationException;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

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
 * Box for the server status.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Box
 */
class ServerStatusBoxController extends AbstractBoxController implements IConditionBoxController
{
    /**
     * selected server id
     */
    public int $serverID = 0;

    /**
     * @inheritDoc
     */
    protected function getAdditionalData(): array
    {
        return [
            'serverID' => $this->serverID
        ];
    }

    /**
     * @inheritDoc
     */
    public function getConditionDefinition(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getConditionObjectTypes(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getConditionsTemplate(): string
    {
        $servers = ServerCache::getInstance()->getServers();
        foreach ($servers as $server) {
            $className = 'rp\system\server\status\\' . \strtoupper($server->getGame()->identifier) . 'ServerStatus';
            if (!\class_exists($className)) continue;

            if (!isset($validServers[$server->getGame()->identifier])) $validServers[$server->getGame()->identifier] = [];
            if (!isset($validServers[$server->getGame()->identifier][$server->serverGroup])) $validServers[$server->getGame()->identifier][$server->serverGroup] = [];
            $validServers[$server->getGame()->identifier][$server->serverGroup][] = $server;
        }

        if (!empty($validServers)) {
            return WCF::getTPL()->fetch('boxServerStatusCondition', 'rp', [
                    'boxController' => $this,
                    'serverID' => $this->serverID,
                    'validServers' => $validServers,
                    ], true);
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    protected function loadContent(): void
    {
        $server = ServerCache::getInstance()->getServerByID($this->serverID);
        if ($server !== null) {
            $className = 'rp\system\server\status\\' . \strtoupper($server->getGame()->identifier) . 'ServerStatus';
            if (!\class_exists($className)) {
                throw new SystemException("Unable to find server status for '" . $server->getGame()->identifier . "'");
            }

            $object = new $className();
            if (!($object instanceof IServerStatus)) {
                throw new ImplementationException(\get_class($object), IServerStatus::class);
            }
            $object->setServer($server);

            $this->content = WCF::getTPL()->fetch('boxServerStatus', 'rp', ['object' => $object], true);
        }
    }

    /**
     * @inheritDoc
     */
    public function readConditions(): void
    {
        if (isset($_POST['serverID'])) {
            $this->serverID = \intval($_POST['serverID']);
        }
    }

    /**
     * @inheritDoc
     */
    public function setBox(Box $box, $setConditionData = true): void
    {
        parent::setBox($box);

        if ($this->box->serverID) {
            $this->serverID = $this->box->serverID;
        }
    }

    /**
     * @inheritDoc
     */
    public function validateConditions(): void
    {
        if ($this->serverID) {
            $server = ServerCache::getInstance()->getServerByID($this->serverID);
            if ($server === null) {
                throw new UserInputException('serverID', 'invalidServer');
            }
        }
    }
}
