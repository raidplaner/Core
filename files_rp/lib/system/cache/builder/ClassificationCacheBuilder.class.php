<?php

namespace rp\system\cache\builder;

use rp\data\classification\Classification;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;


/**
 * Caches the classifications.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class ClassificationCacheBuilder extends AbstractCacheBuilder
{

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [
            'classification' => [],
            'identifier' => [],
        ];

        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_classification
                WHERE   gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$parameters['gameID']]);

        /** @var Classification $object */
        while ($object = $statement->fetchObject(Classification::class)) {
            $data['classification'][$object->classificationID] = $object;
            $data['identifier'][$object->identifier] = $object->classificationID;
        }

        return $data;
    }
}
