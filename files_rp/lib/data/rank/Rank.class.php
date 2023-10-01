<?php

namespace rp\data\rank;

use rp\data\game\Game;
use rp\data\game\GameCache;
use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;


/**
 * Represents a rank.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @property-read   int         $rankID         unique id of the rank
 * @property-read   string      $rankName       name of the rank
 * @property-read   int         $gameID         id of the game
 * @property-read   string      $prefix         prefix of the rank
 * @property-read   string      $suffix         suffix of the rank
 * @property-read   int         $showOrder      position of the rank in relation to game id
 * @property-read   int         $isDefault      is `1` if the rank is the default rank for this game, otherwise `0`
 */
class Rank extends DatabaseObject implements ITitledObject
{

    /**
     * Returns game object.
     */
    public function getGame(): Game
    {
        return GameCache::getInstance()->getGameByID($this->gameID);
    }

    /**
     * Returns the rank with the given rank name.
     */
    public static function getRankByRankname(string $name): Rank
    {
        $sql = "SELECT      *
                FROM        rp" . WCF_N . "_rank
                WHERE       rankName = ?
                    AND     gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $name,
            RP_DEFAULT_GAME_ID,
        ]);
        $row = $statement->fetchArray();
        if (!$row) $row = [];

        return new Rank(null, $row);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->rankName;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
