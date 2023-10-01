<?php

namespace rp\data\rank;

use wcf\data\DatabaseObjectList;


/**
 * Represents a list of ranks.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      Rank        current()
 * @method      Rank[]      getObjects()
 * @method      Rank|null   search($objectID)
 * @property    Rank[]      $objects
 */
class RankList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Rank::class;

    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'showOrder';

}
