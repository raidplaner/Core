<?php

namespace rp\data\rank;

use rp\system\cache\builder\RankCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\WCF;


/**
 * Provides functions to edit rank.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method static   Rank    create(array $parameters = [])
 * @method          Rank    getDecoratedObject()
 * @mixin           Rank
 */
class RankEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Rank::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        RankCacheBuilder::getInstance()->reset();
    }

    /**
     * Sets current rank as default rank.
     */
    public function setAsDefault(): void
    {
        // remove default flag from all ranks
        $sql = "UPDATE	rp" . WCF_N . "_rank
                SET     isDefault = ?
                WHERE	isDefault = ?
                    AND gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            0,
            1,
            $this->gameID,
        ]);

        // set current rank as default rank
        $this->update(['isDefault' => 1]);

        self::resetCache();
    }
}
