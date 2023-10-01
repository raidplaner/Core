<?php

namespace rp\system\page\handler;

use rp\data\character\CharacterProfileList;
use rp\system\cache\runtime\CharacterRuntimeCache;
use wcf\system\page\handler\ILookupPageHandler;


/**
 * Provides the `isValid` and `lookup` methods for looking up characters.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
trait TCharacterLookupPageHandler
{

    /**
     * Returns true if provided object id exists and is valid.
     *
     * @param   int $objectID   page object id
     * @see     ILookupPageHandler::isValid()
     */
    public function isValid($objectID): bool
    {
        return CharacterRuntimeCache::getInstance()->getObject($objectID) !== null;
    }

    /**
     * Performs a search for pages using a query string, returning an array containing
     * an `objectID => title` relation.
     *
     * @param   string      $searchString search string
     * @return  string[]
     * @see     ILookupPageHandler::lookup()
     */
    public function lookup($searchString): array
    {
        $characterList = new CharacterProfileList();
        $characterList->getConditionBuilder()->add('member.characterName LIKE ?', ['%' . $searchString . '%']);
        $characterList->readObjects();

        $results = [];
        foreach ($characterList as $character) {
            $results[] = [
                'image' => $character->getAvatar()->getImageTag(48),
                'link' => $this->getLink($character->characterID),
                'objectID' => $character->characterID,
                'title' => $character->characterName,
            ];
        }

        return $results;
    }
}
