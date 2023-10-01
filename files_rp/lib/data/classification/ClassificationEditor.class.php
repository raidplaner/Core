<?php

namespace rp\data\classification;

use rp\system\cache\builder\ClassificationCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;


/**
 * Provides functions to edit classification.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method static   Classification      create(array $parameters = [])
 * @method          Classification      getDecoratedObject()
 * @mixin           Classification
 */
class ClassificationEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Classification::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        ClassificationCacheBuilder::getInstance()->reset();
    }
}
