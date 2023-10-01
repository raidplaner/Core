<?php

namespace rp\system\cache\builder;

use rp\data\faction\Faction;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;


/**
 * Caches the faction.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class FactionCacheBuilder extends AbstractCacheBuilder
{

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [
            'faction' => [],
            'identifier' => [],
        ];

        // get game faction
        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_faction
                WHERE   isDisabled = ?
                    AND gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            0,
            $parameters['gameID'],
        ]);

        /** @var Faction $object */
        while ($object = $statement->fetchObject(Faction::class)) {
            $data['faction'][$object->factionID] = $object;
            $data['identifier'][$object->identifier] = $object->factionID;
        }

        return $data;
    }
}
