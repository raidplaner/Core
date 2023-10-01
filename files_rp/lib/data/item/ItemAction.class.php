<?php

namespace rp\data\item;

use rp\data\point\account\PointAccountCache;
use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use rp\system\cache\runtime\RaidRuntimeCache;
use rp\util\RPUtil;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IPopoverAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;


/**
 * Executes item-related actions.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      ItemEditor[]    getObjects()
 * @method      ItemEditor      getSingleObject()
 */
class ItemAction extends AbstractDatabaseObjectAction implements IPopoverAction
{
    /**
     * @inheritDoc
     */
    protected $allowGuestAccess = ['getPopover', 'load'];

    /**
     * @inheritDoc
     */
    protected $className = ItemEditor::class;

    /**
     * @inheritDoc
     */
    public function create(): Item
    {
        $this->parameters['data']['date'] = TIME_NOW;

        return parent::create();
    }

    /**
     * @inheritDoc
     */
    public function getPopover(): array
    {
        $itemID = \reset($this->objectIDs);

        if ($itemID) {
            $item = ItemCache::getInstance()->getItemByID($itemID);
            if ($item && $item->template) {
                $template = \str_replace('{ITEM_ICON}', $item->getIconURL(), $item->template);
                WCF::getTPL()->assign('template', $template);
            } else {
                WCF::getTPL()->assign('unknownItem', true);
            }
        } else {
            WCF::getTPL()->assign('unknownItem', true);
        }

        return [
            'template' => WCF::getTPL()->fetch('itemPreview', 'rp'),
        ];
    }

    /**
     * Loads a list of items.
     */
    public function load(): array
    {
        $sql = "SELECT      item_to_raid.*, raid.date
                FROM        rp" . WCF_N . "_item_to_raid item_to_raid
                LEFT JOIN   rp" . WCF_N . "_raid raid
                ON          item_to_raid.raidID = raid.raidID
                WHERE       item_to_raid.characterID = ?
                ORDER BY    raid.date DESC";
        $statement = WCF::getDB()->prepareStatement($sql, 6, $this->parameters['lastItemOffset']);
        $statement->execute([$this->parameters['characterID']]);

        $items = [];
        while ($row = $statement->fetchArray()) {
            $items[] = [
                'item' => ItemCache::getInstance()->getItemByID($row['itemID']),
                'pointAccount' => PointAccountCache::getInstance()->getPointAccountByID($row['pointAccountID']),
                'points' => RPUtil::formatPoints($row['points']),
                'raid' => RaidRuntimeCache::getInstance()->getObject($row['raidID'])
            ];
        }

        if (empty($items)) {
            return [];
        }

        // parse template
        WCF::getTPL()->assign([
            'items' => $items,
        ]);

        return [
            'lastItemOffset' => $this->parameters['lastItemOffset'] + 6,
            'template' => WCF::getTPL()->fetch('characterProfileItemItem', 'rp'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function validateGetPopover(): void
    {
        if (\count($this->objectIDs) != 1) {
            throw new UserInputException('objectIDs');
        }
    }

    /**
     * Validates parameters to load items.
     */
    public function validateLoad()
    {
        $this->readInteger('lastItemOffset', true);
        $this->readInteger('characterID');

        $character = CharacterProfileRuntimeCache::getInstance()->getObject($this->parameters['characterID']);
        if ($character === null) {
            throw new IllegalLinkException();
        }
    }
}
