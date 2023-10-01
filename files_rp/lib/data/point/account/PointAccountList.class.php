<?php

namespace rp\data\point\account;

use wcf\data\DatabaseObjectList;


/**
 * Represents a list of point accounts.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      PointAccount        current()
 * @method      PointAccount[]      getObjects()
 * @method      PointAccount|null   search($objectID)
 * @property    PointAccount[]      $objects
 */
class PointAccountList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = PointAccount::class;

    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'showOrder';

}
