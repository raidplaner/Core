<?php

namespace rp\system\cache\builder;

use rp\data\server\Server;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;


/**
 * Caches the server.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class ServerCacheBuilder extends AbstractCacheBuilder
{

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [
            'group' => [],
            'identifier' => [],
            'server' => [],
        ];

        // get game server
        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_server
                WHERE   gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$parameters['gameID']]);

        /** @var Server $object */
        while ($object = $statement->fetchObject(Server::class)) {
            if (!isset($data['group'][$object->serverGroup])) $data['group'][$object->serverGroup] = [];
            $data['group'][$object->serverGroup][] = $object->serverID;

            $data['identifier'][$object->identifier] = $object->serverID;

            $data['server'][$object->serverID] = $object;
        }

        return $data;
    }
}
