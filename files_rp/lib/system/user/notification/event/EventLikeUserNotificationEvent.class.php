<?php

namespace rp\system\user\notification\event;

use rp\data\event\LikeableEvent;
use rp\system\cache\runtime\ViewableEventRuntimeCache;
use wcf\data\user\UserProfile;
use wcf\system\user\notification\event\AbstractSharedUserNotificationEvent;
use wcf\system\user\notification\event\ITestableUserNotificationEvent;
use wcf\system\user\notification\event\TReactionUserNotificationEvent;
use wcf\system\user\notification\event\TTestableLikeUserNotificationEvent;
use wcf\system\user\notification\event\TTestableUserNotificationEvent;
use wcf\system\user\notification\object\LikeUserNotificationObject;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;


/**
 * User notification event for event likes.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      LikeUserNotificationObject  getUserNotificationObject()
 */
class EventLikeUserNotificationEvent extends AbstractSharedUserNotificationEvent implements ITestableUserNotificationEvent
{
    use TTestableLikeUserNotificationEvent {
        TTestableLikeUserNotificationEvent::canBeTriggeredByGuests insteadof TTestableUserNotificationEvent;
    }
    use TTestableEventUserNotificationEvent;
    use TTestableUserNotificationEvent;
    use TReactionUserNotificationEvent;
    /**
     * @inheritDoc
     */
    protected $stackable = true;

    /**
     * @inheritDoc
     */
    public function checkAccess(): bool
    {
        if (!ViewableEventRuntimeCache::getInstance()->getObject($this->getUserNotificationObject()->objectID)->canRead()) {
            UserStorageHandler::getInstance()->reset([WCF::getUser()->userID], 'rpUnreadEvents');

            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected static function createTestLikeObject(UserProfile $recipient, UserProfile $author): LikeableEvent
    {
        return new LikeableEvent(self::getTestEvent($author));
    }

    /**
     * @inheritDoc
     */
    public function getEmailMessage($notificationType = 'instant'): void
    {
        // not supported
    }

    /**
     * @inheritDoc
     */
    public function getEventHash(): string
    {
        return \sha1($this->eventID . '-' . $this->additionalData['objectID']);
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return ViewableEventRuntimeCache::getInstance()
                ->getObject($this->getUserNotificationObject()->objectID)
                ->getLink();
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        $event = ViewableEventRuntimeCache::getInstance()->getObject($this->getUserNotificationObject()->objectID);
        $authors = \array_values($this->getAuthors());
        $count = \count($authors);

        if ($count > 1) {
            return $this->getLanguage()->getDynamicVariable('rp.event.like.notification.message.stacked', [
                    'author' => $this->author,
                    'authors' => $authors,
                    'count' => $count,
                    'event' => $event,
                    'others' => $count - 1,
                    'reactions' => $this->getReactionsForAuthors(),
            ]);
        }

        return $this->getLanguage()->getDynamicVariable('rp.event.like.notification.message', [
                'author' => $this->author,
                'event' => $event,
                'reactions' => $this->getReactionsForAuthors(),
                'userNotificationObject' => $this->getUserNotificationObject(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected static function getTestLikeableObjectTypeName(): string
    {
        return 'dev.daries.rp.likeableEvent';
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        $count = \count($this->getAuthors());
        if ($count > 1) {
            return $this->getLanguage()->getDynamicVariable('rp.event.like.notification.title.stacked', [
                    'count' => $count,
                    'timesTriggered' => $this->notification->timesTriggered,
            ]);
        }

        return $this->getLanguage()->getDynamicVariable('rp.event.like.notification.title');
    }

    /**
     * @inheritDoc
     */
    public function isVisible(): bool
    {
        if (!MODULE_LIKE) {
            return false;
        }

        return parent::isVisible();
    }

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        ViewableEventRuntimeCache::getInstance()->cacheObjectID($this->getUserNotificationObject()->objectID);
    }

    /**
     * @inheritDoc
     */
    public function supportsEmailNotification(): bool
    {
        return false;
    }
}
