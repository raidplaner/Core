<?php

namespace rp\system\cronjob;

use rp\data\event\EventAction;
use wcf\data\cronjob\Cronjob;
use wcf\system\cronjob\AbstractCronjob;
use wcf\system\WCF;


/**
 * Deletes thrashed calendar events.
 * 
 * @author  Marco Daries
 * @package     Daries\RP
 */
class EmptyRecycleBinCronjob extends AbstractCronjob
{

    /**
     * @inheritDoc
     */
    public function execute(Cronjob $cronjob)
    {
        parent::execute($cronjob);

        if (RP_EVENT_EMPTY_RECYCLE_BIN_CYCLE) {
            $sql = "SELECT	eventID
                    FROM	rp" . WCF_N . "_event
                    WHERE	isDeleted = ?
                        AND deleteTime < ?";
            $statement = WCF::getDB()->prepareStatement($sql, 1000);
            $statement->execute([
                1,
                TIME_NOW - RP_EVENT_EMPTY_RECYCLE_BIN_CYCLE * 86400,
            ]);
            $eventIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);

            if (!empty($eventIDs)) {
                $action = new EventAction($eventIDs, 'delete');
                $action->executeAction();
            }
        }
    }
}
