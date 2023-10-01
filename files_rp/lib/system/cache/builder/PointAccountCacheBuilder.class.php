<?php

namespace rp\system\cache\builder;

use rp\data\point\account\PointAccount;
use rp\data\raid\event\RaidEventList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;


/**
 * Caches the point accounts.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class PointAccountCacheBuilder extends AbstractCacheBuilder
{

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [
            'accounts' => [],
            'raidEvents' => []
        ];

        // get point accounts
        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_point_account
                WHERE   gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$parameters['gameID']]);

        /** @var PointAccount $object */
        while ($object = $statement->fetchObject(PointAccount::class)) {
            $data['accounts'][$object->pointAccountID] = $object;

            $raidEventList = new RaidEventList();
            $raidEventList->getConditionBuilder()->add('gameID = ?', [$parameters['gameID']]);
            $raidEventList->getConditionBuilder()->add('pointAccountID = ?', [$object->pointAccountID]);
            $raidEventList->readObjects();
            foreach ($raidEventList as $raidEvent) {
                if (!isset($data['raidEvents'][$object->pointAccountID])) $data['raidEvents'][$object->pointAccountID] = [];
                $data['raidEvents'][$object->pointAccountID][$raidEvent->eventID] = $raidEvent;
            }
        }

        return $data;
    }
}
