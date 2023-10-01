<?php

namespace rp\data\server;

use rp\system\cache\builder\ServerCacheBuilder;
use wcf\system\SingletonFactory;


/**
 * Manages the server cache.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class ServerCache extends SingletonFactory
{
    /**
     * cached server ids with server identifier as key
     * @var int[]
     */
    protected $cachedIdentifier;

    /**
     * cached servers
     * @var Server[]
     */
    protected $cachedServers = [];

    /**
     * Returns the server with the given server id or `null` if no such server exists.
     */
    public function getServerByID(int $serverID): ?Server
    {
        return $this->cachedServers[$serverID] ?? null;
    }

    /**
     * Returns the server with the given server identifier or `null` if no such server exists.
     */
    public function getServerByIdentifier(string $identifier): ?Server
    {
        return $this->getServerByID($this->cachedIdentifier[$identifier] ?? 0);
    }

    /**
     * Returns all servers.
     * 
     * @return  Server[]
     */
    public function getServers(): array
    {
        return $this->cachedServers;
    }

    /**
     * Returns the server with the given server id.
     * 
     * @return	Server[]
     */
    public function getServersByID(array $serverIDs): array
    {
        $returnValues = [];

        foreach ($serverIDs as $serverID) {
            $returnValues[] = $this->getServerByID($serverID);
        }

        return $returnValues;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedServers = ServerCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'server');
        $this->cachedIdentifier = ServerCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'identifier');
    }
}
