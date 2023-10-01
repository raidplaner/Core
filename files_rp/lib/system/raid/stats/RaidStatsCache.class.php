<?php

namespace rp\system\raid\stats;

use wcf\system\SingletonFactory;
use wcf\system\WCF;


/**
 * Caches raid stats.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class RaidStatsCache extends SingletonFactory
{

    public function getStats(): array
    {
        $stats = [];

        $sql = "SELECT      raid.date, raidEvent.pointAccountID
                FROM        rp" . WCF_N . "_raid raid
                LEFT JOIN   rp" . WCF_N . "_raid_event raidEvent
                ON          raid.raidEventID = raidEvent.eventID";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute();
        while ($row = $statement->fetchArray()) {
            if (!isset($stats[$row['pointAccountID']])) {
                $stats[$row['pointAccountID']] = [
                    'raid30' => 0,
                    'raid60' => 0,
                    'raid90' => 0,
                    'raidAll' => 0
                ];
            }

            $stats[$row['pointAccountID']]['raidAll']++;
            if ($row['date'] >= (TIME_NOW - (90 * 86400))) $stats[$row['pointAccountID']]['raid90']++;
            if ($row['date'] >= (TIME_NOW - (60 * 86400))) $stats[$row['pointAccountID']]['raid60']++;
            if ($row['date'] >= (TIME_NOW - (30 * 86400))) $stats[$row['pointAccountID']]['raid30']++;
        }

        return $stats;
    }
}
