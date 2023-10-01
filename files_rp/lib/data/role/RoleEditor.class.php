<?php

namespace rp\data\role;

use rp\system\cache\builder\RoleCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;


/**
 * Provides functions to edit role.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method static   Role    create(array $parameters = [])
 * @method          Role    getDecoratedObject()
 * @mixin           Role
 */
class RoleEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Role::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        RoleCacheBuilder::getInstance()->reset();
    }
}
