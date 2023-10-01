<?php

namespace rp\system\event\listener;

use rp\system\cache\builder\StatsCacheBuilder;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\WCF;


/**
 * @author  Marco Daries
 * @package     Daries\RP
 */
class StatisticsBoxControllerListener implements IParameterizedEventListener
{

    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        WCF::getTPL()->assign([
            'rpStatistics' => StatsCacheBuilder::getInstance()->getData(),
        ]);
    }
}
