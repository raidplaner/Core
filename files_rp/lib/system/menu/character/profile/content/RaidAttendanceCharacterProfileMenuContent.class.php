<?php

namespace rp\system\menu\character\profile\content;

use rp\data\raid\event\RaidEventCache;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\SingletonFactory;
use wcf\system\WCF;


/**
 * Handles character profile raid attendance content.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class RaidAttendanceCharacterProfileMenuContent extends SingletonFactory implements ICharacterProfileMenuContent
{

    /**
     * @inheritDoc
     */
    public function getContent(int $characterID): string
    {
        $raidEvents = RaidEventCache::getInstance()->getRaidEvents();

        $sql = "SELECT      raidEventID, COUNT(*) as count
                FROM        rp" . WCF_N . "_raid
                GROUP BY    raidEventID";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        $maxRaids = $statement->fetchMap('raidEventID', 'count');

        $characterRaids = [];
        $sql = "SELECT  raidID
                FROM    rp" . WCF_N . "_raid_attendee
                WHERE   characterID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$characterID]);
        $raidIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);

        if (!empty($raidIDs)) {
            $conditions = new PreparedStatementConditionBuilder();
            $conditions->add("raidID IN (?)", [$raidIDs]);

            $sql = "SELECT      raidEventID, COUNT(*) as count
                    FROM        rp" . WCF_N . "_raid
                    " . $conditions . "
                    GROUP BY    raidEventID";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute($conditions->getParameters());
            $characterRaids = $statement->fetchMap('raidEventID', 'count');
        }

        foreach ($raidEvents as $raidEvent) {
            $raidStats[$raidEvent->eventID] = [
                'is' => $characterRaids[$raidEvent->eventID] ?? 0,
                'max' => $maxRaids[$raidEvent->eventID] ?? 0,
                'percent' => 0
            ];

            if (!$raidStats[$raidEvent->eventID]['is'] && !$raidStats[$raidEvent->eventID]['max']) $raidStats[$raidEvent->eventID]['percent'] = 0;
            else $raidStats[$raidEvent->eventID]['percent'] = \number_format(($raidStats[$raidEvent->eventID]['is'] / $raidStats[$raidEvent->eventID]['max']) * 100);
        }

        WCF::getTPL()->assign([
            'characterID' => $characterID,
            'raidEvents' => $raidEvents,
            'raidStats' => $raidStats
        ]);

        return WCF::getTPL()->fetch('characterProfileRaidAttendance', 'rp');
    }

    /**
     * @inheritDoc
     */
    public function isVisible(int $characterID): bool
    {
        return true;
    }
}
