<?php

namespace rp\page;

use rp\data\character\CharacterProfileList;
use rp\data\point\account\PointAccountCache;
use wcf\page\MultipleLinkPage;
use wcf\system\WCF;


/**
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class PointListPage extends MultipleLinkPage
{
    /**
     * available letters
     */
    public static string $availableLetters = '#ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * @inheritDoc
     */
    public $itemsPerPage = 60;

    /**
     * letter
     */
    public string $letter = '';

    /**
     * @inheritDoc
     */
    public $objectListClassName = CharacterProfileList::class;

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
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'letter' => $this->letter,
            'letters' => \str_split(self::$availableLetters),
            'pointAccounts' => PointAccountCache::getInstance()->getPointAccounts()
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList(): void
    {
        parent::initObjectList();

        $this->objectList->getConditionBuilder()->add('isDisabled = ?', [0]);
        if (!RP_SHOW_TWINKS) $this->objectList->getConditionBuilder()->add('isPrimary = ?', [1]);

        if (!empty($this->letter)) {
            if ($this->letter == '#') {
                $this->objectList->getConditionBuilder()->add("SUBSTRING(characterName,1,1) IN ('0', '1', '2', '3', '4', '5', '6', '7', '8', '9')");
            } else {
                $this->objectList->getConditionBuilder()->add("characterName LIKE ?", [$this->letter . '%']);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        // letter
        if (isset($_REQUEST['letter']) && \mb_strlen($_REQUEST['letter']) == 1 && \mb_strpos(self::$availableLetters, $_REQUEST['letter']) !== false) {
            $this->letter = $_REQUEST['letter'];
        }
    }
}
