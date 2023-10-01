<?php

namespace rp\system\moderation\queue\report;

use rp\system\moderation\queue\AbstractEventModerationQueueHandler;
use wcf\data\moderation\queue\ViewableModerationQueue;
use wcf\system\moderation\queue\report\IModerationQueueReportHandler;


/**
 * An implementation of IModerationQueueReportHandler for events.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Moderation\Queue\Report
 */
class EventModerationQueueReportHandler extends AbstractEventModerationQueueHandler implements IModerationQueueReportHandler
{
    /**
     * @inheritDoc
     */
    protected $definitionName = 'com.woltlab.wcf.moderation.report';

    /**
     * @inheritDoc
     */
    public function canReport($objectID): bool
    {
        if (!$this->isValid($objectID)) {
            return false;
        }

        if (!$this->getEvent($objectID)->canRead()) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getReportedContent(ViewableModerationQueue $queue): string
    {
        return $this->getRelatedContent($queue);
    }

    /**
     * @inheritDoc
     */
    public function getReportedObject($objectID)
    {
        return $this->getEvent($objectID);
    }
}
