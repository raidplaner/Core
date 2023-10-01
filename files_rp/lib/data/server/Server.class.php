<?php

namespace rp\data\server;

use rp\data\game\Game;
use rp\data\game\GameCache;
use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;


/**
 * Represents a server.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @property-read   int         $serverID               unique id of the server
 * @property-read   int         $packageID              id of the package which delivers the server
 * @property-read   int         $gameID                 id of the game
 * @property-read   string      $identifier             unique textual identifier of the identifier
 * @property-read   string      $type                   type of the server
 * @property-read   int         $serverGroup            group of the server
 */
class Server extends DatabaseObject implements ITitledObject
{

    /**
     * Returns game object.
     */
    public function getGame(): ?Game
    {
        return GameCache::getInstance()->getGameByID($this->gameID);
    }

    /**
     * Returns language group name.
     */
    public function getGroupName(): string
    {
        return WCF::getLanguage()->get('rp.server.' . $this->getGame()->identifier . '.group.' . $this->serverGroup);
    }

    /**
     * Returns the image folder of the game.
     * 
     * @since 2.0
     */
    public function getImagePath(): string
    {
        return WCF::getPath('rp') . 'images/' . $this->getGame()->identifier . '/';
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return WCF::getLanguage()->get('rp.server.' . $this->getGame()->identifier . '.' . $this->identifier);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
