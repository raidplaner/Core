<?php

namespace rp\data\character\avatar;

use wcf\data\DatabaseObjectList;


/**
 * Represents a list of avatars.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      CharacterAvatar         current()
 * @method      CharacterAvatar[]       getObjects()
 * @method      CharacterAvatar|null    search($objectID)
 * @property    CharacterAvatar[]       $objects
 */
class CharacterAvatarList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = CharacterAvatar::class;

}
