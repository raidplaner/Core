<?php

namespace rp\system\server\status;

use rp\data\server\Server;


/**
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
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
