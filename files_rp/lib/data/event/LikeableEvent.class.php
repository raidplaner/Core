<?php

namespace rp\data\event;

use wcf\data\like\Like;
use wcf\data\like\object\AbstractLikeObject;
use wcf\data\reaction\object\IReactionObject;
use wcf\system\user\notification\object\LikeUserNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\WCF;


/**
 * Likeable object implementation for events.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      Event   getDecoratedObject()
 * @mixin       Event
 */
class LikeableEvent extends AbstractLikeObject implements IReactionObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Event::class;

    /**
     * @inheritDoc
     */
    public function getLanguageID(): ?int
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getObjectID(): int
    {
        return $this->eventID;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->getDecoratedObject()->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getURL(): string
    {
        return $this->getDecoratedObject()->getLink();
    }

    /**
     * @inheritDoc
     */
    public function getUserID(): int
    {
        return $this->userID;
    }

    /**
     * @inheritDoc
     */
    public function sendNotification(Like $like): void
    {
        if ($this->getDecoratedObject()->userID != WCF::getUser()->userID) {
            $notificationObject = new LikeUserNotificationObject($like);
            UserNotificationHandler::getInstance()->fireEvent(
                'like',
                'dev.daries.rp.likeableEvent.notification',
                $notificationObject,
                [$this->getDecoratedObject()->userID],
                ['objectID' => $this->getDecoratedObject()->entryID]
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function updateLikeCounter($cumulativeLikes): void
    {
        // update cumulative likes
        $editor = new EventEditor($this->getDecoratedObject());
        $editor->update(['cumulativeLikes' => $cumulativeLikes]);
    }
}
