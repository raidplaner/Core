<?php

namespace rp\data\event;


/**
 * Represents a list of deleted events.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class DeletedEventList extends ViewableEventList
{

    public function __construct()
    {
        parent::__construct();

        $this->getConditionBuilder()->add('event.isDeleted = ?', [1]);
    }
}
