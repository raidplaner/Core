<?php

namespace rp\system\cache\builder;

use rp\data\game\Game;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;


/**
 * Caches the games.
 * 
 * @author  Marco Daries
 * @package     Daries\RP\System\Cache\Builder
 */
class GameCacheBuilder extends AbstractCacheBuilder
{

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [
            'games' => [],
            'identifier' => [],
        ];

        // get games
        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_game";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();

        /** @var Game $object */
        while ($object = $statement->fetchObject(Game::class)) {
            $data['games'][$object->gameID] = $object;
            $data['identifier'][$object->identifier] = $object->gameID;
        }

        return $data;
    }
}
