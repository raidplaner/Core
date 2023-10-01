<?php

namespace rp\data\event;

use wcf\data\search\ISearchResultObject;
use wcf\system\html\output\HtmlOutputProcessor;
use wcf\system\request\LinkHandler;
use wcf\system\search\SearchResultTextParser;


/**
 * Represents an event as a search result.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class SearchResultEvent extends ViewableEvent implements ISearchResultObject
{

    /**
     * @inheritDoc
     */
    public function getContainerLink(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getContainerTitle(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getFormattedMessage(): string
    {
        $processor = new HtmlOutputProcessor();
        $processor->setOutputType('text/simplified-html');
        $processor->process(
            $this->notes,
            'dev.daries.rp.event.notes',
            $this->eventID,
            false
        );
        $message = SearchResultTextParser::getInstance()->parse($processor->getHtml());

        return $message;
    }

    /**
     * @inheritDoc
     */
    public function getLink($query = ''): string
    {
        $parameters = [
            'application' => 'rp',
            'forceFrontend' => true,
            'object' => $this->getDecoratedObject(),
        ];

        if ($query) {
            $parameters['highlight'] = \urlencode($query);
        }

        return LinkHandler::getInstance()->getLink('Event', $parameters);
    }

    /**
     * @inheritDoc
     */
    public function getObjectTypeName(): string
    {
        return 'dev.daries.rp.event';
    }

    /**
     * @inheritDoc
     */
    public function getSubject(): string
    {
        return $this->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getTime(): int
    {
        return $this->created;
    }
}
