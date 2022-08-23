<?php

namespace rp\system\importer;

use wcf\data\object\type\ObjectTypeCache;
use wcf\system\importer\AbstractCommentImporter;
use wcf\system\importer\ImportHandler;

/**
 *  Project:    Raidplaner: Core
 *  Package:    info.daries.rp
 *  Link:       http://daries.info
 *
 *  Copyright (C) 2018-2022 Daries.info Developer Team
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
 * Imports event comments.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Importer
 */
class EventCommentImporter extends AbstractCommentImporter
{
    /**
     * @inheritDoc
     */
    protected $objectTypeName = 'info.daries.rp.event.comment';

    /**
     * Creates a new EventCommentImporter object.
     */
    public function __construct()
    {
        $objectType = ObjectTypeCache::getInstance()
            ->getObjectTypeByName('com.woltlab.wcf.comment.commentableContent', 'info.daries.rp.eventComment');
        $this->objectTypeID = $objectType->objectTypeID;
    }

    /**
     * @inheritDoc
     */
    public function import($oldID, array $data, array $additionalData = []): mixed
    {
        $eventID = ImportHandler::getInstance()->getNewID('info.daries.rp.event', $data['objectID'] ?? $additionalData['eventID']);
        if (!$eventID) return 0;
        $data['objectID'] = $eventID;

        return parent::import($oldID, $data);
    }
}
