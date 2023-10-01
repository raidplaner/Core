<?php

namespace rp\system\user\notification\event;

use rp\system\cache\runtime\ViewableEventRuntimeCache;
use wcf\system\user\notification\event\AbstractSharedUserNotificationEvent;
use wcf\system\user\notification\event\ITestableUserNotificationEvent;
use wcf\system\user\notification\event\TTestableCommentUserNotificationEvent;
use wcf\system\user\notification\object\CommentUserNotificationObject;


/**
 * User notification event for event comments.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      CommentUserNotificationObject   getUserNotificationObject()
 */
class EventCommentUserNotificationEvent extends AbstractSharedUserNotificationEvent implements ITestableUserNotificationEvent
{
    use TTestableCommentUserNotificationEvent;
    use TTestableEventCommentUserNotificationEvent;
    /**
     * @inheritDoc
     */
    protected $stackable = true;

    /**
     * @inheritDoc
     */
    public function getEmailMessage($notificationType = 'instant'): array
    {
        return [
            'message-id' => 'dev.daries.rp.eventComment.notification/' . $this->getUserNotificationObject()->commentID,
            'template' => 'email_notification_comment',
            'application' => 'wcf',
            'variables' => [
                'commentID' => $this->getUserNotificationObject()->commentID,
                'eventObj' => ViewableEventRuntimeCache::getInstance()
                    ->getObject($this->getUserNotificationObject()->objectID),
                'languageVariablePrefix' => 'rp.user.notification.eventComment',
            ],
        ];
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
        return ViewableEventRuntimeCache::getInstance()->getObject($this->getUserNotificationObject()->objectID)->getLink() . '#comment' . $this->getUserNotificationObject()->commentID;
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        $authors = $this->getAuthors();
        if (\count($authors) > 1) {
            if (isset($authors[0])) {
                unset($authors[0]);
            }
            $count = \count($authors);

            return $this->getLanguage()->getDynamicVariable('rp.user.notification.eventComment.message.stacked', [
                    'author' => $this->author,
                    'authors' => \array_values($authors),
                    'commentID' => $this->getUserNotificationObject()->commentID,
                    'count' => $count,
                    'event' => ViewableEventRuntimeCache::getInstance()
                        ->getObject($this->getUserNotificationObject()->objectID),
                    'guestTimesTriggered' => $this->notification->guestTimesTriggered,
                    'others' => $count - 1,
            ]);
        }

        return $this->getLanguage()->getDynamicVariable('rp.user.notification.eventComment.message', [
                'author' => $this->author,
                'commentID' => $this->getUserNotificationObject()->commentID,
                'event' => ViewableEventRuntimeCache::getInstance()
                    ->getObject($this->getUserNotificationObject()->objectID),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        $count = \count($this->getAuthors());
        if ($count > 1) {
            return $this->getLanguage()->getDynamicVariable('rp.user.notification.eventComment.title.stacked', [
                    'count' => $count,
                    'timesTriggered' => $this->notification->timesTriggered,
            ]);
        }

        return $this->getLanguage()->getDynamicVariable('rp.user.notification.eventComment.title');
    }

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        ViewableEventRuntimeCache::getInstance()->cacheObjectID($this->getUserNotificationObject()->objectID);
    }
}
