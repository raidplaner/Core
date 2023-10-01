<?php

namespace rp\system\server\status;

use rp\data\server\Server;


/**
 * Any game server status should implement this interface.
 * 
 * @author  Marco Daries
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
