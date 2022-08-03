<?php

namespace rp\system\importer;

use rp\data\character\Character;
use rp\data\character\CharacterEditor;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;
use wcf\system\WCF;

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
 * Imports characters.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Importer
 */
class CharacterImporter extends AbstractImporter
{
    /**
     * @inheritDoc
     */
    protected $className = Character::class;

    /**
     * @inheritDoc
     */
    public function import($oldID, array $data, array $additionalData = []): mixed
    {
        // check existing character id
        if (\ctype_digit((string) $oldID)) {
            $character = new Character($oldID);
            if (!$character->characterID) $data['characterID'] = $oldID;
        }

        // fetch character with same character name
        $conflictingCharacter = Character::getCharacterByCharactername($data['characterName']);
        if ($conflictingCharacter->characterID) {
            $data['characterName'] = self::resolveDuplicate($data['characterName']);
        }
        
        $data['gameID'] ??= RP_DEFAULT_GAME_ID;

        // get user id
        $data['userID'] = ImportHandler::getInstance()->getNewID('com.woltlab.wcf.user', $data['userID']);
        // get rank id
        $data['rankID'] = ImportHandler::getInstance()->getNewID('info.daries.rp.rank', $data['rankID']);

        // create character
        $character = CharacterEditor::create($data);

        // save mapping
        ImportHandler::getInstance()->saveNewID('info.daries.rp.character', $oldID, $character->characterID);

        return $character->characterID;
    }

    /**
     * Revolves duplicate character names and returns the new character name.
     */
    private static function resolveDuplicate(string $characterName): string
    {
        $i = 0;
        do {
            $i++;
            $newUsername = 'Duplicate' . ($i > 1 ? $i : '') . ' ' . $characterName;
            // try character name
            $sql = "SELECT  characterID
                    FROM    rp" . WCF_N . "_member
                    WHERE   characterName = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$newUsername]);
            $row = $statement->fetchArray();
            if (empty($row['characterID'])) break;
        }
        while (true);

        return $newUsername;
    }
}
