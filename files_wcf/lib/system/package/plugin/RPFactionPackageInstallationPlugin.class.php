<?php

namespace wcf\system\package\plugin;

use rp\data\faction\FactionEditor;
use wcf\system\devtools\pip\IIdempotentPackageInstallationPlugin;
use wcf\system\exception\SystemException;
use wcf\system\WCF;

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
 * Installs, updates and deletes factions.
 * 
 * @author      Marco Daries
 * @package     WoltLabSuite\Core\System\Package\Plugin
 */
class RPFactionPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin implements IIdempotentPackageInstallationPlugin
{
    /**
     * @inheritDoc
     */
    public $application = 'rp';

    /**
     * @inheritDoc
     */
    public $className = FactionEditor::class;

    /**
     * @inheritDoc
     */
    public $tableName = 'faction';

    /**
     * @inheritDoc
     */
    public $tagName = 'faction';

    /**
     * @inheritDoc
     */
    protected function findExistingItem(array $data): array
    {
        $sql = "SELECT	*
                FROM	" . $this->application . WCF_N . "_" . $this->tableName . "
                WHERE	identifier = ?
                    AND packageID = ?";
        $parameters = [
            $data['identifier'],
            $this->installation->getPackageID(),
        ];

        return [
            'sql' => $sql,
            'parameters' => $parameters,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getDefaultFilename(): string
    {
        return 'rpFaction.xml';
    }

    /**
     * @inheritDoc
     */
    public static function getSyncDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function handleDelete(array $items): void
    {
        $sql = "DELETE FROM " . $this->application . WCF_N . "_" . $this->tableName . "
                WHERE       identifier = ?
                    AND     packageID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);

        WCF::getDB()->beginTransaction();
        foreach ($items as $item) {
            $statement->execute([
                $item['attributes']['identifier'],
                $this->installation->getPackageID(),
            ]);
        }
        WCF::getDB()->commitTransaction();
    }

    /**
     * @inheritDoc
     */
    protected function prepareImport(array $data): array
    {
        $gameID = null;
        if (!empty($data['elements']['game'])) {
            $sql = "SELECT  gameID
                    FROM    " . $this->application . WCF_N . "_game
                    WHERE   identifier = ?";
            $statement = WCF::getDB()->prepareStatement($sql, 1);
            $statement->execute([$data['elements']['game']]);
            $row = $statement->fetchSingleRow();
            if ($row === false) {
                throw new SystemException("Unable to find game '" . $data['elements']['game'] . "' for faction '" . $data['attributes']['identifier'] . "'");
            }

            $gameID = $row['gameID'];
        }

        if ($gameID === null) {
            throw new SystemException("The faction '" . $data['attributes']['identifier'] . "' must either have an associated game.");
        }

        return [
            'gameID' => $gameID,
            'icon' => $data['elements']['icon'] ?? '',
            'identifier' => $data['attributes']['identifier'],
        ];
    }
}
