<?php

namespace rp\system\cache\builder;

use rp\data\rank\Rank;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;


/**
 * Caches the ranks.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class RankCacheBuilder extends AbstractCacheBuilder
{

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [
            'default' => [],
            'ranks' => [],
        ];

        // get ranks
        $sql = "SELECT      *
                FROM        rp" . WCF_N . "_rank
                WHERE       gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$parameters['gameID']]);

        /** @var Rank $object */
        while ($object = $statement->fetchObject(Rank::class)) {
            $data['ranks'][$object->rankID] = $object;
            if ($object->isDefault) $data['default'] = $object->rankID;
        }

        return $data;
    }
}
