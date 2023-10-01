<?php

namespace rp\data\game;

use rp\system\cache\builder\GameCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;


/**
 * Provides functions to edit games.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method static   Game    create(array $parameters = [])
 * @method          Game    getDecoratedObject()
 * @mixin           Game
 */
class GameEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Game::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        GameCacheBuilder::getInstance()->reset();
    }
}
