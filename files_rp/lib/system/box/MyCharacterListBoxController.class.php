<?php

namespace rp\system\box;

use rp\data\character\CharacterProfileList;
use wcf\data\DatabaseObjectList;
use wcf\system\box\AbstractDatabaseObjectListBoxController;
use wcf\system\WCF;


/**
 * Box controller for a list of my characters.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class MyCharacterListBoxController extends AbstractDatabaseObjectListBoxController
{
    /**
     * @inheritDoc
     */
    public $sortField = 'characterName';

    /**
     * @inheritDoc
     */
    public $sortOrder = 'ASC';

    /**
     * @inheritDoc
     */
    protected static $supportedPositions = [
        'sidebarLeft',
        'sidebarRight',
    ];

    /**
     * @inheritDoc
     */
    protected function getObjectList(): DatabaseObjectList
    {
        $list = new CharacterProfileList();
        $list->getConditionBuilder()->add('userID = ?', [WCF::getUser()->userID]);
        $list->getConditionBuilder()->add('gameID = ?', [RP_DEFAULT_GAME_ID]);
        $list->getConditionBuilder()->add('isDisabled = ?', [0]);

        return $list;
    }

    /**
     * @inheritDoc
     */
    public function getTemplate(): string
    {
        return WCF::getTPL()->fetch('boxMyCharactersList', 'rp', [
                'boxCharacterList' => $this->objectList,
                ], true);
    }
}
