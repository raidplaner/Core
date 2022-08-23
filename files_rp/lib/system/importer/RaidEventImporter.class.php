<?php

namespace rp\system\importer;

use rp\data\raid\event\RaidEvent;
use rp\data\raid\event\RaidEventEditor;
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
 * Imports raid events.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Importer
 */
class RaidEventImporter extends AbstractImporter
{
    /**
     * @inheritDoc
     */
    protected $className = RaidEvent::class;

    /**
     * @inheritDoc
     */
    public function import($oldID, array $data, array $additionalData = []): mixed
    {
        $data['pointAccountID'] = ImportHandler::getInstance()->getNewID('info.daries.rp.point.account', $data['pointAccountID']);
        $data['gameID'] ??= RP_DEFAULT_GAME_ID;

        if (isset($additionalData['iconLocation'])) {
            if (!\file_exists(RP_DIR . 'images/raid/event/' . $data['icon'] . '.png')) {
                if (!@\copy($additionalData['iconLocation'], RP_DIR . 'images/raid/event/' . $data['icon'] . '.png')) {
                    $data['icon'] = '';
                }
            }
        }

        $event = RaidEventEditor::create($data);
        $newID = $event->eventID;

        // handle i18n values
        if (!empty($additionalData['i18n'])) {
            $values = [];

            if (isset($additionalData['i18n']['eventName'])) {
                $values['eventName'] = $additionalData['i18n']['eventName'];
            }

            if (!empty($values)) {
                $updateData = [];
                if (isset($values['eventName'])) {
                    $updateData['eventName'] = 'rp.raid.event.event' . $newID;
                }

                $items = [];
                foreach ($values as $property => $propertyValues) {
                    foreach ($propertyValues as $languageID => $languageItemValue) {
                        $items[] = [
                            'languageID' => $languageID,
                            'languageItem' => 'rp.raid.event.event' . $newID,
                            'languageItemValue' => $languageItemValue,
                        ];
                    }
                }

                $this->importI18nValues($items, 'rp.raid.event', 'info.daries.rp');

                (new RaidEventEditor($event))->update($updateData);
            }
        }

        ImportHandler::getInstance()->saveNewID('info.daries.rp.raid.event', $oldID, $newID);
        
        return $newID;
    }
}
