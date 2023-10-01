<?php

namespace rp\data\game;

use wcf\data\DatabaseObjectList;


/**
 * Represents a list of games.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      Game        current()
 * @method      Game[]      getObjects()
 * @method      Game|null   search($objectID)
 * @property    Game[]      $objects
 */
class GameList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Game::class;

}
