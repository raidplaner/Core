<?php

namespace rp\system\cache\builder;

use rp\data\raid\event\I18nRaidEventList;
use wcf\system\cache\builder\AbstractCacheBuilder;


/**
 * Caches the raid event.
 * 
 * @author  Marco Daries
 * @package     Daries\RP\System\Cache\Builder
 */
class RaidEventCacheBuilder extends AbstractCacheBuilder
{

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $list = new I18nRaidEventList();
        $list->getConditionBuilder()->add('gameID = ?', [$parameters['gameID']]);
        $list->readObjects();
        return $list->getObjects();
    }
}
