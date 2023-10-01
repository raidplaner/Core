<?php

namespace rp\system\menu\character\profile\content;

use rp\data\item\ItemCache;
use rp\data\point\account\PointAccountCache;
use rp\system\cache\runtime\RaidRuntimeCache;
use rp\util\RPUtil;
use wcf\system\SingletonFactory;
use wcf\system\WCF;


/**
 * Handles character profile item content.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Menu\Character\Profile\Content
 */
class ItemCharacterProfileMenuContent extends SingletonFactory implements ICharacterProfileMenuContent
{

    /**
     * @inheritDoc
     */
    public function getContent(int $characterID): string
    {
        $sql = "SELECT      item_to_raid.*, raid.date
                FROM        rp" . WCF_N . "_item_to_raid item_to_raid
                LEFT JOIN   rp" . WCF_N . "_raid raid
                ON          item_to_raid.raidID = raid.raidID
                WHERE       item_to_raid.characterID = ?
                ORDER BY    raid.date DESC";
        $statement = WCF::getDB()->prepareStatement($sql, 10);
        $statement->execute([$characterID]);

        $items = [];
        while ($row = $statement->fetchArray()) {
            $items[] = [
                'item' => ItemCache::getInstance()->getItemByID($row['itemID']),
                'pointAccount' => PointAccountCache::getInstance()->getPointAccountByID($row['pointAccountID']),
                'points' => RPUtil::formatPoints($row['points']),
                'raid' => RaidRuntimeCache::getInstance()->getObject($row['raidID'])
            ];
        }

        $items = \array_slice($items, 0, 6);

        WCF::getTPL()->assign([
            'characterID' => $characterID,
            'items' => $items,
            'lastItemOffset' => 6
        ]);

        return WCF::getTPL()->fetch('characterProfileItem', 'rp');
    }

    /**
     * @inheritDoc
     */
    public function isVisible(int $characterID): bool
    {
        if (!RP_ENABLE_ITEM) return false;

        return true;
    }
}
