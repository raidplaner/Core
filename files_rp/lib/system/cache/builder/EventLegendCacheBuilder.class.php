<?php

namespace rp\system\cache\builder;

use rp\data\event\legend\EventLegendList;
use wcf\system\cache\builder\AbstractCacheBuilder;


/**
 * Caches the event legends.
 * 
 * @author  Marco Daries
 * @package     Daries\RP\System\Cache\Builder
 */
class EventLegendCacheBuilder extends AbstractCacheBuilder
{

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $legendList = new EventLegendList();
        $legendList->sqlOrderBy = 'name ASC';
        $legendList->readObjects();
        return $legendList->getObjects();
    }
}
