<?php

namespace rp\system\server\status;

use rp\data\server\Server;


/**
 * @author  Marco Daries
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
