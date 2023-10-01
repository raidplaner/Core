<?php

namespace rp\data\character;

use wcf\data\DatabaseObjectEditor;


/**
 * Provides functions to edit characters.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method static   Character   create(array $parameters = [])
 * @method          Character   getDecoratedObject()
 * @mixin           Character
 */
class CharacterEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Character::class;

}
