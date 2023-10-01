<?php

namespace rp\system\cache\builder;

use rp\data\race\Race;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;


/**
 * Caches the race.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class RaceCacheBuilder extends AbstractCacheBuilder
{

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [
            'identifier' => [],
            'race' => [],
        ];

        // get game race
        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_race
                WHERE   gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$parameters['gameID']]);

        /** @var Race $object */
        while ($object = $statement->fetchObject(Race::class)) {
            $data['identifier'][$object->identifier] = $object->raceID;
            $data['race'][$object->raceID] = $object;
        }

        return $data;
    }
}
