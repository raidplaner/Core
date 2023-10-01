<?php

namespace rp\system\cache\builder;

use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;


/**
 * Caches the number of events and the events per day.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Cache\Builder
 */
class StatsCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected $maxLifetime = 1200;

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters)
    {
        $data = [];

        // number of characters
        $sql = "SELECT  COUNT(*)
                FROM    rp" . WCF_N . "_member
                WHERE   isDisabled = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([0]);
        $data['characters'] = $statement->fetchSingleColumn();

        // number of comments
        $sql = "SELECT  SUM(comments)
                FROM    rp" . WCF_N . "_event
                WHERE   comments > 0";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        $data['comments'] = $statement->fetchSingleColumn();

        // number of events
        $sql = "SELECT  COUNT(*)
                FROM    rp" . WCF_N . "_event";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        $data['events'] = $statement->fetchSingleColumn();

        // number of raids
        $sql = "SELECT  COUNT(*)
                FROM    rp" . WCF_N . "_raid";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        $data['raids'] = $statement->fetchSingleColumn();

        // raids/events per Day
        $days = \ceil((TIME_NOW - RP_INSTALL_DATE) / 86400);
        if ($days <= 0) {
            $days = 1;
        }
        $data['eventsPerDay'] = $data['events'] / $days;
        $data['raidsPerDay'] = $data['raids'] / $days;

        return $data;
    }
}
