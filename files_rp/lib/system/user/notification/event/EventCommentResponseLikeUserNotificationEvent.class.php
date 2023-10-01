<?php

namespace rp\system\user\notification\event;

use rp\system\cache\runtime\ViewableEventRuntimeCache;
use wcf\system\cache\runtime\UserRuntimeCache;
use wcf\system\user\notification\event\AbstractSharedUserNotificationEvent;
use wcf\system\user\notification\event\ITestableUserNotificationEvent;
use wcf\system\user\notification\event\TReactionUserNotificationEvent;
use wcf\system\user\notification\event\TTestableCommentResponseLikeUserNotificationEvent;
use wcf\system\user\notification\object\LikeUserNotificationObject;
use wcf\system\WCF;


/**
 * User notification event for event comment response likes.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\User\Notification\Object\Type
 *
 * @method      LikeUserNotificationObject  getUserNotificationObject()
 */
class EventCommentResponseLikeUserNotificationEvent extends AbstractSharedUserNotificationEvent implements
ITestableUserNotificationEvent
{
    use TTestableCommentResponseLikeUserNotificationEvent;
    use TTestableEventCommentUserNotificationEvent;
    use TReactionUserNotificationEvent;
    /**
     * @inheritDoc
     */
    protected $stackable = true;

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
        return \sha1($this->eventID . '-' . $this->getUserNotificationObject()->objectID);
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return ViewableEventRuntimeCache::getInstance()->getObject($this->additionalData['objectID'])->getLink()
            . '#comment' . $this->additionalData['commentID'] . '/response' . $this->getUserNotificationObject()->objectID;
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        $event = ViewableEventRuntimeCache::getInstance()->getObject($this->additionalData['objectID']);
        $authors = \array_values($this->getAuthors());
        $count = \count($authors);
        $commentUser = null;
        if ($this->additionalData['commentUserID'] != WCF::getUser()->userID) {
            $commentUser = UserRuntimeCache::getInstance()->getObject($this->additionalData['commentUserID']);
        }

        if ($count > 1) {
            return $this->getLanguage()->getDynamicVariable(
                    'rp.user.notification.eventComment.response.like.message.stacked',
                    [
                        'author' => $this->author,
                        'authors' => $authors,
                        'commentID' => $this->additionalData['commentID'],
                        'commentUser' => $commentUser,
                        'count' => $count,
                        'event' => $event,
                        'others' => $count - 1,
                        'reactions' => $this->getReactionsForAuthors(),
                        'responseID' => $this->getUserNotificationObject()->objectID,
                    ]
            );
        }

        return $this->getLanguage()->getDynamicVariable('rp.user.notification.eventComment.response.like.message', [
                'author' => $this->author,
                'commentID' => $this->additionalData['commentID'],
                'event' => $event,
                'reactions' => $this->getReactionsForAuthors(),
                'responseID' => $this->getUserNotificationObject()->objectID,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        $count = \count($this->getAuthors());
        if ($count > 1) {
            return $this->getLanguage()->getDynamicVariable(
                    'rp.user.notification.eventComment.response.like.title.stacked',
                    [
                        'count' => $count,
                        'timesTriggered' => $this->notification->timesTriggered,
                    ]
            );
        }

        return $this->getLanguage()->get('rp.user.notification.eventComment.response.like.title');
    }

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        ViewableEventRuntimeCache::getInstance()->cacheObjectID($this->additionalData['objectID']);
        UserRuntimeCache::getInstance()->cacheObjectID($this->additionalData['commentUserID']);
    }

    /**
     * @inheritDoc
     */
    public function supportsEmailNotification(): bool
    {
        return false;
    }
}
