<?php

namespace rp\system\importer;

use rp\data\event\legend\EventLegend;
use rp\data\event\legend\EventLegendEditor;
use wcf\system\importer\AbstractImporter;
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
 * Imports event legends.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Importer
 */
class EventLegendImporter extends AbstractImporter
{
    /**
     * @inheritDoc
     */
    protected $className = EventLegend::class;

    /**
     * @inheritDoc
     */
    public function import($oldID, array $data, array $additionalData = []): mixed
    {
        // create legend
        $legend = EventLegendEditor::create($data);

        // save mapping
        ImportHandler::getInstance()->saveNewID('info.daries.rp.event.legend', $oldID, $legend->legendID);

        return $legend->legendID;
    }
}
