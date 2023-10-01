<?php

namespace rp\data\point\account;

use rp\system\cache\builder\CharacterPointCacheBuilder;
use rp\system\cache\builder\PointAccountCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\WCF;


/**
 * Provides functions to edit point account.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method static   PointAccount    create(array $parameters = [])
 * @method          PointAccount    
 */
class PointAccountEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = PointAccount::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        PointAccountCacheBuilder::getInstance()->reset();
        CharacterPointCacheBuilder::getInstance()->reset();
    }

    /**
     * Adds the point account to a specific position.
     */
    public function setShowOrder(int $showOrder = 0): void
    {
        // shift back point accounts with higher showOrder
        if ($this->showOrder) {
            $sql = "UPDATE  rp" . WCF_N . "_point_account
                    SET     showOrder = showOrder - 1
                    WHERE   gameID = ?
                        AND showOrder >= ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$this->gameID, $this->showOrder]);
        }

        // shift point accounts with higher showOrder
        if ($showOrder) {
            $sql = "UPDATE  rp" . WCF_N . "_point_account
                    SET     showOrder = showOrder + 1
                    WHERE   gameID = ?
                        AND showOrder >= ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$this->gameID, $showOrder]);
        }

        // get maximum existing show order
        $sql = "SELECT  MAX(showOrder)
                FROM    rp" . WCF_N . "_point_account
                WHERE   gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$this->gameID]);
        $maxShowOrder = $statement->fetchSingleColumn() ?: 0;

        if (!$showOrder || $showOrder > $maxShowOrder) {
            $showOrder = $maxShowOrder + 1;
        }

        $this->update(['showOrder' => $showOrder]);
    }
}
