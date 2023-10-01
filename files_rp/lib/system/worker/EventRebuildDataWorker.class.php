<?php

namespace rp\system\worker;

use rp\data\event\EventEditor;
use rp\data\event\EventList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\bbcode\BBCodeHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\html\input\HtmlInputProcessor;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\search\SearchIndexManager;
use wcf\system\WCF;
use wcf\system\worker\AbstractRebuildDataWorker;

/**
 * @author  Marco Daries
 * @package     Daries\RP\System\Worker
 * 
 * @method      EventList       getObjectList()
 */
class EventRebuildDataWorker extends AbstractRebuildDataWorker
{
    /**
     * html input processor
     */
    protected ?HtmlInputProcessor $htmlInputProcessor = null;

    /**
     * @inheritDoc
     */
    protected $limit = 100;

    /**
     * @inheritDoc
     */
    protected $objectListClassName = EventList::class;

    /**
     * @inheritDoc
     */
    public function execute(): void
    {
        parent::execute();

        if (!$this->loopCount) {
            SearchIndexManager::getInstance()->reset('dev.daries.rp.event');
        }

        if (!\count($this->objectList)) {
            return;
        }

        // fetch cumulative likes
        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add("objectTypeID = ?", [ObjectTypeCache::getInstance()->getObjectTypeIDByName('com.woltlab.wcf.like.likeableObject', 'dev.daries.rp.likeableEvent')]);
        $conditions->add("objectID IN (?)", [$this->objectList->getObjectIDs()]);

        $sql = "SELECT	objectID, cumulativeLikes
                FROM	wcf" . WCF_N . "_like_object
                " . $conditions;
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute($conditions->getParameters());
        $cumulativeLikes = $statement->fetchMap('objectID', 'cumulativeLikes');

        // retrieve permissions
        $userIDs = [];
        foreach ($this->objectList as $object) {
            $userIDs[] = $object->userID;
        }
        $userPermissions = $this->getBulkUserPermissions($userIDs, ['user.message.disallowedBBCodes']);

        $commentObjectType = ObjectTypeCache::getInstance()
            ->getObjectTypeByName('com.woltlab.wcf.comment.commentableContent', 'dev.daries.rp.eventComment');
        $sql = "SELECT  COUNT(*) AS comments, SUM(responses) AS responses
                FROM    wcf" . WCF_N . "_comment
                WHERE   objectTypeID = ?
                    AND objectID = ?";
        $commentStatement = WCF::getDB()->prepareStatement($sql);
        $comments = [];
        foreach ($this->getObjectList() as $event) {
            $commentStatement->execute([$commentObjectType->objectTypeID, $event->eventID]);
            $row = $commentStatement->fetchSingleRow();
            if (!isset($comments[$event->eventID])) {
                $comments[$event->eventID] = 0;
            }
            $comments[$event->eventID] += $row['comments'] + $row['responses'];
        }

        foreach ($this->objectList as $event) {
            $editor = new EventEditor($event);
            $data = [];

            // update cumulative likes
            $data['cumulativeLikes'] = $cumulativeLikes[$event->eventID] ?? 0;

            // update comment counter
            $data['comments'] = $comments[$event->eventID] ?? 0;

            BBCodeHandler::getInstance()->setDisallowedBBCodes(\explode(',', $this->getBulkUserPermissionValue($userPermissions, $event->userID, 'user.message.disallowedBBCodes')));

            $this->getHtmlInputProcessor()->reprocess(
                $event->notes,
                'dev.daries.rp.event.notes',
                $event->eventID
            );
            $data['notes'] = $this->getHtmlInputProcessor()->getHtml();

            if (MessageEmbeddedObjectManager::getInstance()->registerObjects($this->getHtmlInputProcessor())) {
                $data['hasEmbeddedObjects'] = 1;
            } else {
                $data['hasEmbeddedObjects'] = 0;
            }

            // save updated event data
            $editor->update($data);

            // update search index
            SearchIndexManager::getInstance()->set(
                'dev.daries.rp.event',
                $event->eventID,
                $event->notes,
                $event->getTitle(),
                $event->created,
                $event->userID,
                $event->username
            );
        }
    }

    /**
     * @return HtmlInputProcessor
     */
    protected function getHtmlInputProcessor(): HtmlInputProcessor
    {
        if ($this->htmlInputProcessor === null) {
            $this->htmlInputProcessor = new HtmlInputProcessor();
        }

        return $this->htmlInputProcessor;
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList(): void
    {
        parent::initObjectList();

        $this->objectList->sqlOrderBy = 'event.eventID';
    }
}
