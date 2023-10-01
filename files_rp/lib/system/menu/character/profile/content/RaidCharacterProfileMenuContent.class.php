<?php

namespace rp\system\menu\character\profile\content;

use rp\data\raid\RaidList;
use wcf\system\SingletonFactory;
use wcf\system\WCF;


/**
 * Handles character profile raid content.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Menu\Character\Profile\Content
 */
class RaidCharacterProfileMenuContent extends SingletonFactory implements ICharacterProfileMenuContent
{

    /**
     * @inheritDoc
     */
    public function getContent(int $characterID): string
    {
        $raidList = new RaidList();
        $raidList->sqlJoins = "
                LEFT JOIN   rp" . WCF_N . "_raid_attendee raid_attendee
                ON          raid.raidID = raid_attendee.raidID";
        $raidList->getConditionBuilder()->add('raid_attendee.characterID = ?', [$characterID]);
        $raidList->sqlOrderBy = 'raid.date DESC, raid.raidID DESC';

        // load more items than necessary to avoid empty list if some items are invisible for current character
        $raidList->sqlLimit = 10;

        $raidList->readObjects();

        // remove unused items
        $raidList->truncate(6);

        WCF::getTPL()->assign([
            'characterID' => $characterID,
            'lastRaidTime' => $raidList->getLastRaidTime(),
            'raidList' => $raidList,
        ]);

        return WCF::getTPL()->fetch('characterProfileRaid', 'rp');
    }

    /**
     * @inheritDoc
     */
    public function isVisible(int $characterID): bool
    {
        return true;
    }
}
