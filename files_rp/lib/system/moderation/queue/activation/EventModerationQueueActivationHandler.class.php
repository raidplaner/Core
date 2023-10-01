<?php

namespace rp\system\moderation\queue\activation;

use rp\data\event\EventAction;
use rp\system\moderation\queue\AbstractEventModerationQueueHandler;
use wcf\data\moderation\queue\ModerationQueue;
use wcf\data\moderation\queue\ViewableModerationQueue;
use wcf\system\moderation\queue\activation\IModerationQueueActivationHandler;


/**
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class EventModerationQueueActivationHandler extends AbstractEventModerationQueueHandler implements IModerationQueueActivationHandler
{
    /**
     * @inheritDoc
     */
    protected $definitionName = 'com.woltlab.wcf.moderation.activation';

    /**
     * @inheritDoc
     */
    public function enableContent(ModerationQueue $queue): void
    {
        if ($this->isValid($queue->objectID) && $this->getEvent($queue->objectID)->isDisabled) {
            $eventAction = new EventAction([$this->getEvent($queue->objectID)], 'enable');
            $eventAction->executeAction();
        }
    }

    /**
     * @inheritDoc
     */
    public function getDisabledContent(ViewableModerationQueue $queue): string
    {
        return $this->getRelatedContent($queue);
    }
}
