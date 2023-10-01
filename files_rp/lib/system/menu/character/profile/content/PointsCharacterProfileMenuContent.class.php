<?php

namespace rp\system\menu\character\profile\content;

use rp\data\point\account\PointAccount;
use rp\data\point\account\PointAccountCache;
use rp\system\cache\builder\CharacterPointCacheBuilder;
use rp\system\cache\runtime\CharacterRuntimeCache;
use rp\system\raid\stats\RaidStatsCache;
use rp\util\RPUtil;
use wcf\system\SingletonFactory;
use wcf\system\WCF;


/**
 * Handles character profile points content.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class PointsCharacterProfileMenuContent extends SingletonFactory implements ICharacterProfileMenuContent
{

    /**
     * @inheritDoc
     */
    public function getContent(int $characterID): string
    {
        $character = CharacterRuntimeCache::getInstance()->getObject($characterID);
        if ($character === null) return '';

        $primaryCharacter = $character->getPrimaryCharacter();
        $characterPoints = CharacterPointCacheBuilder::getInstance()->getData(['primaryCharacterID' => $primaryCharacter->characterID]);
        if (!isset($characterPoints[$characterID])) return '';
        $characterPoints = $characterPoints[$characterID];

        $pointAccounts = PointAccountCache::getInstance()->getPointAccounts();
        $raidStats = RaidStatsCache::getInstance()->getStats();

        $sql = "SELECT      raid.date, raidEvent.pointAccountID
                FROM        rp" . WCF_N . "_raid raid
                LEFT JOIN   rp" . WCF_N . "_raid_attendee attendee
                ON          raid.raidID = attendee.raidID
                LEFT JOIN   rp" . WCF_N . "_raid_event raidEvent
                ON          raid.raidEventID = raidEvent.eventID
                WHERE       attendee.characterID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$characterID]);
        $characterStats = [];
        $raidDates = $statement->fetchMap('pointAccountID', 'date', false);
        foreach ($pointAccounts as $pointAccountID => $pointAccount) {
            $characterStats[$pointAccountID] = [];
            foreach (['raid30', 'raid60', 'raid90', 'raidAll'] as $type) {
                $characterStats[$pointAccountID][$type] = [
                    'color' => 'red',
                    'is' => 0,
                    'max' => $raidStats[$pointAccountID][$type] ?? 0,
                    'percent' => 0,
                ];
            };

            if (isset($raidDates[$pointAccountID])) {
                foreach ($raidDates[$pointAccountID] as $date) {
                    $characterStats[$pointAccountID]['raidAll']['is']++;
                    if ($date >= (TIME_NOW - (90 * 86400))) $characterStats[$pointAccountID]['raid90']['is']++;
                    if ($date >= (TIME_NOW - (60 * 86400))) $characterStats[$pointAccountID]['raid60']['is']++;
                    if ($date >= (TIME_NOW - (30 * 86400))) $characterStats[$pointAccountID]['raid30']['is']++;
                }
            }

            foreach (['raid30', 'raid60', 'raid90', 'raidAll'] as $type) {
                if (!$characterStats[$pointAccountID][$type]['is'] &&
                    !$characterStats[$pointAccountID][$type]['max']) {
                    continue;
                }

                $characterStats[$pointAccountID][$type]['percent'] = \number_format(
                    ($characterStats[$pointAccountID][$type]['is'] /
                    $characterStats[$pointAccountID][$type]['max']) * 100
                );

                if ($characterStats[$pointAccountID][$type]['percent'] >= 40 &&
                    $characterStats[$pointAccountID][$type]['percent'] < 80) {
                    $characterStats[$pointAccountID][$type]['color'] = 'yellow';
                } else if ($characterStats[$pointAccountID][$type]['percent'] >= 80) {
                    $characterStats[$pointAccountID][$type]['color'] = 'green';
                }
            }

            foreach (['adjustments', 'current', 'issued', 'received'] as $type) {
                $characterPoints[$pointAccountID][$type]['points'] = RPUtil::formatPoints($characterPoints[$pointAccountID][$type]['points']);
            }
        }

        \usort($pointAccounts, static function (PointAccount $a, PointAccount $b) {
            return \strcasecmp($a->getTitle(), $b->getTitle());
        });

        WCF::getTPL()->assign([
            'characterPoints' => $characterPoints,
            'characterStats' => $characterStats,
            'pointAccounts' => $pointAccounts,
        ]);

        return WCF::getTPL()->fetch('characterProfilePoints', 'rp');
    }

    public function isVisible(int $characterID): bool
    {
        return true;
    }
}
