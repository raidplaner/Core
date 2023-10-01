<?php

namespace rp\data\server;

use rp\system\cache\builder\ServerCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;


/**
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method static   Server      create(array $parameters = [])
 * @method          Server      getDecoratedObject()
 * @mixin           Server
 */
class ServerEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Server::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        ServerCacheBuilder::getInstance()->reset();
    }
}
