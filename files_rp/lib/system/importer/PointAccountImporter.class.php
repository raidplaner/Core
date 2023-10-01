<?php

namespace rp\system\importer;

use rp\data\point\account\PointAccount;
use rp\data\point\account\PointAccountEditor;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;

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
 * Imports point accounts (DKP).
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Importer
 */
class PointAccountImporter extends AbstractImporter
{
    /**
     * @inheritDoc
     */
    protected $className = PointAccount::class;

    /**
     * @inheritDoc
     */
    public function import($oldID, array $data, array $additionalData = []): mixed
    {
        $data['gameID'] ??= RP_DEFAULT_GAME_ID;

        $pointAccount = PointAccountEditor::create($data);
        $newID = $pointAccount->pointAccountID;

        // handle i18n values
        if (!empty($additionalData['i18n'])) {
            $values = [];

            foreach (['pointAccountName', 'description'] as $property) {
                if (isset($additionalData['i18n'][$property])) {
                    $values[$property] = $additionalData['i18n'][$property];
                }
            }

            if (!empty($values)) {
                $updateData = [];
                if (isset($values['pointAccountName'])) {
                    $updateData['pointAccountName'] = 'rp.acp.point.account.account' . $newID;
                }
                if (isset($values['description'])) {
                    $updateData['description'] = 'rp.acp.point.account.account' . $newID . '.description';
                }

                $items = [];
                foreach ($values as $property => $propertyValues) {
                    foreach ($propertyValues as $languageID => $languageItemValue) {
                        $items[] = [
                            'languageID' => $languageID,
                            'languageItem' => 'rp.acp.point.account.account' . $newID . ($property === 'description' ? '.description' : ''),
                            'languageItemValue' => $languageItemValue,
                        ];
                    }
                }

                $this->importI18nValues($items, 'rp.acp.point', 'dev.daries.rp');

                (new PointAccountEditor($pointAccount))->update($updateData);
            }
        }

        // save mapping
        ImportHandler::getInstance()->saveNewID('dev.daries.rp.point.account', $oldID, $newID);

        return $newID;
    }
}
