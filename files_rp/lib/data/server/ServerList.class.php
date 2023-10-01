<?php

namespace rp\data\server;

use wcf\data\DatabaseObjectList;


/**
 * Represents a list of servers.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      Server          current()
 * @method      Server[]        getObjects()
 * @method      Server|null     search($objectID)
 * @property    Server[]        $objects
 */
class ServerList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Server::class;

}
