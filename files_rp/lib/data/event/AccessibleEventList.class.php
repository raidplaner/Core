<?php

namespace rp\data\event;

use wcf\system\WCF;


/**
 * Represents a list of accessible events.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class AccessibleEventList extends ViewableEventList
{

    /**
     * Creates a new AccessibleEventList object.
     */
    public function __construct(int $start = 0, int $end = 0)
    {
        parent::__construct();

        if ($start && $end) {
            $this->getConditionBuilder()->add('((event.startTime >= ? AND event.startTime < ?) OR (event.startTime < ? AND event.endTime >= ?))', [$start, $end, $start, $start]);
        } else if ($start) {
            $this->getConditionBuilder()->add('(event.startTime >= ? OR (event.startTime <= ? AND event.endTime > ?))', [$start, $start, $start]);
        } else if ($end) {
            $this->getConditionBuilder()->add('event.endTime < ?', [$end]);
        }

        // default conditions
        if (!WCF::getSession()->getPermission('mod.rp.canModerateEvent')) $this->getConditionBuilder()->add('event.isDisabled = ?', [0]);
        if (!WCF::getSession()->getPermission('mod.rp.canViewDeletedEvent')) $this->getConditionBuilder()->add('event.isDeleted = ?', [0]);
    }

    /**
     * @inheritDoc
     */
    public function readObjects(): void
    {
        if ($this->objectIDs === null) $this->readObjectIDs();

        parent::readObjects();
    }
}
