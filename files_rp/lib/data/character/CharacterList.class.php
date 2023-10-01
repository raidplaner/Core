<?php

namespace rp\data\character;

use wcf\data\DatabaseObjectList;


/**
 * Represents a list of characters.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      Character           current()
 * @method      Character[]         getObjects()
 * @method      Character|null      search($objectID)
 * @property	Character[]         $objects
 */
class CharacterList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Character::class;

}
