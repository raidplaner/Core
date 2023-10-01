<?php

namespace rp\data\point\account;

use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;


/**
 * Represents a point account.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @property-read   int         $pointAccountID     unique id of the point account
 * @property-read   string      $pointAccountName   name of the point account or name of language item which contains the name
 * @property-read   string      $description        description of the point account
 * @property-read   int         $gameID             id of the game
 * @property-read   int         $showOrder          position of the point account
 */
class PointAccount extends DatabaseObject implements ITitledObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'pointAccountID';

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return WCF::getLanguage()->get($this->pointAccountName);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
