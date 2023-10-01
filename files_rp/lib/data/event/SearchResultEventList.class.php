<?php

namespace rp\data\event;


/**
 * Represents a list of event as search results.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      SearchResultEvent       current()
 * @method      SearchResultEvent[]     getObjects()
 * @method      SearchResultEvent|null  getSingleObject()
 * @method      SearchResultEvent|null  search($objectID)
 * @property    SearchResultEvent[]     $objects
 */
class SearchResultEventList extends ViewableEventList
{
    /**
     * @inheritDoc
     */
    public $decoratorClassName = SearchResultEvent::class;

}
