<?php

namespace rp\system\importer;

use rp\data\character\Character;
use rp\data\character\CharacterEditor;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;
use wcf\system\WCF;


/**
 * Imports characters.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
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
        $data['rankID'] = ImportHandler::getInstance()->getNewID('dev.daries.rp.rank', $data['rankID']);

        // create character
        $character = CharacterEditor::create($data);

        // save mapping
        ImportHandler::getInstance()->saveNewID('dev.daries.rp.character', $oldID, $character->characterID);

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
