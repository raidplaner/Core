<?php

namespace rp\data\role;

use wcf\data\DatabaseObjectList;


/**
 * Represents a list of roles.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      Role        current()
 * @method      Role[]      getObjects()
 * @method      Role|null   search($objectID)
 * @property    Role[]      $objects
 */
class RoleList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Role::class;

}
