<?php

namespace rp\system\search;

use rp\data\event\SearchResultEvent;
use rp\data\event\SearchResultEventList;
use wcf\data\search\ISearchResultObject;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\search\AbstractSearchProvider;
use wcf\system\WCF;


/**
 * An implementation of ISearchProvider for searching in events.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class EventSearch extends AbstractSearchProvider
{
    /**
     * @var SearchResultEvent[]
     */
    private array $messageCache = [];

    /**
     * @inheritDoc
     */
    public function cacheObjects(array $objectIDs, ?array $additionalData = null): void
    {
        $list = new SearchResultEventList();
        $list->setObjectIDs($objectIDs);
        $list->readObjects();
        foreach ($list->getObjects() as $event) {
            $this->messageCache[$event->eventID] = $event;
        }
    }

    /**
     * @inheritDoc
     */
    public function getConditionBuilder(array $parameters): ?PreparedStatementConditionBuilder
    {
        $conditionBuilder = new PreparedStatementConditionBuilder();

        $conditionBuilder->add($this->getTableName() . '.isDisabled = 0');
        $conditionBuilder->add($this->getTableName() . '.isDeleted = 0');

        return $conditionBuilder;
    }

    /**
     * @inheritDoc
     */
    public function getIDFieldName(): string
    {
        return $this->getTableName() . '.eventID';
    }

    /**
     * @inheritDoc
     */
    public function getObject(int $objectID): ?ISearchResultObject
    {
        return $this->messageCache[$objectID] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getSubjectFieldName(): string
    {
        return $this->getTableName() . '.title';
    }

    /**
     * @inheritDoc
     */
    public function getTableName(): string
    {
        return 'rp' . WCF_N . '_event';
    }

    /**
     * @inheritDoc
     */
    public function getTimeFieldName(): string
    {
        return $this->getTableName() . '.created';
    }

    /**
     * @inheritDoc
     */
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('user.rp.canReadEvent');
    }
}
