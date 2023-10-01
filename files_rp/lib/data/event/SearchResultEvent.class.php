<?php

namespace rp\data\event;

use wcf\data\search\ISearchResultObject;
use wcf\system\html\output\HtmlOutputProcessor;
use wcf\system\request\LinkHandler;
use wcf\system\search\SearchResultTextParser;

/**
 *  Project:    Raidplaner: Core
 *  Package:    dev.daries.rp
 *  Link:       http://daries.dev
 *
 *  Copyright (C) 2018-2023 Daries.dev Developer Team
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Represents an event as a search result.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Data\Event
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
