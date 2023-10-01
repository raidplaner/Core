<?php

namespace rp\data\raid;

use rp\data\character\Character;
use rp\data\item\Item;
use rp\data\point\account\PointAccount;
use rp\data\point\account\PointAccountCache;
use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use rp\system\cache\runtime\CharacterRuntimeCache;
use rp\system\item\ItemHandler;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;


/**
 * Executes raid related actions.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      RaidEditor[]    getObjects()
 * @method      RaidEditor      getSingleObject()
 */
class RaidAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $allowGuestAccess = ['load'];

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['mod.rp.canAddRaid'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['mod.rp.canDeleteRaid'];

    /**
     * @inheritDoc
     */
    protected $className = RaidEditor::class;

    /**
     * Add attendees to given raid.
     */
    public function addAttendees(): void
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        $attendeeIDs = $this->parameters['attendees'];
        $deleteOldAttendees = true;
        if (isset($this->parameters['deleteOldAttendees'])) {
            $deleteOldAttendees = $this->parameters['deleteOldAttendees'];
        }

        foreach ($this->getObjects() as $raidEditor) {
            $raidEditor->addAttendees($attendeeIDs, $deleteOldAttendees);
        }

        RaidEditor::resetCache();
    }

    /**
     * Add raid items to given raid.
     */
    public function addRaidItems(): void
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        $raidItems = $this->parameters['raidItems'];
        $deleteOldRaidItems = true;
        if (isset($this->parameters['deleteOldRaidItems'])) {
            $deleteOldRaidItems = $this->parameters['deleteOldRaidItems'];
        }

        foreach ($this->getObjects() as $raidEditor) {
            $raidEditor->addRaidItems($raidItems, $deleteOldRaidItems);
        }

        RaidEditor::resetCache();
    }

    /**
     * @inheritDoc
     */
    public function create(): Raid
    {
        $this->parameters['data']['addedBy'] = WCF::getUser()->username;

        $raid = parent::create();
        $raidEditor = new RaidEditor($raid);

        $attendeeIDs = $this->parameters['attendees'] ?? [];
        $raidEditor->addAttendees($attendeeIDs, false, $this->parameters['event']);

        $raidItems = $this->parameters['raidItems'] ?? [];
        $raidEditor->addRaidItems($raidItems, false);

        return $raid;
    }

    /**
     * Loads a list of raids.
     */
    public function load(): array
    {
        $raidList = new RaidList();
        $raidList->sqlJoins = "
                LEFT JOIN   rp" . WCF_N . "_raid_attendee raid_attendee
                ON          raid.raidID = raid_attendee.raidID";
        if ($this->parameters['lastRaidTime']) {
            $raidList->getConditionBuilder()->add("raid.date < ?", [$this->parameters['lastRaidTime']]);
        }
        $raidList->getConditionBuilder()->add('raid_attendee.characterID = ?', [$this->parameters['characterID']]);
        $raidList->sqlOrderBy = 'raid.date DESC, raid.raidID DESC';
        $raidList->sqlLimit = 6;
        $raidList->readObjects();

        if (empty($raidList)) {
            return [];
        }

        // parse template
        WCF::getTPL()->assign([
            'raidList' => $raidList,
        ]);

        return [
            'lastRaidTime' => $raidList->getLastRaidTime(),
            'template' => WCF::getTPL()->fetch('characterProfileRaidItem', 'rp'),
        ];
    }

    public function searchItem(): array
    {
        /** @var Item $item */
        $item = ItemHandler::getInstance()->getSearchItem($this->parameters['itemName']);

        /** @var PointAccount $pointAccount */
        $pointAccount = PointAccountCache::getInstance()->getPointAccountByID($this->parameters['pointAccountID']);

        /** @var Character $character */
        $character = CharacterRuntimeCache::getInstance()->getObject($this->parameters['characterID']);

        return [
            'characterID' => $character->characterID,
            'characterName' => $character->getTitle(),
            'itemID' => $item->itemID,
            'itemName' => $item->itemName,
            'pointAccountID' => $pointAccount->pointAccountID,
            'pointAccountName' => $pointAccount->getTitle(),
            'points' => $this->parameters['points']
        ];
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->parameters['data']['updatedBy'] = WCF::getUser()->username;

        parent::update();

        $attendeeIDs = $this->parameters['attendees'] ?? [];
        if (!empty($attendeeIDs)) {
            $action = new self($this->objects, 'addAttendees', [
                'attendees' => $attendeeIDs
            ]);
            $action->executeAction();
        }

        $raidItems = $this->parameters['raidItems'] ?? [];
        if (!empty($raidItems)) {
            $action = new self($this->objects, 'addRaidItems', [
                'raidItems' => $raidItems
            ]);
            $action->executeAction();
        }
    }

    /**
     * Validates parameters to load raids.
     */
    public function validateLoad()
    {
        $this->readInteger('lastRaidTime', true);
        $this->readInteger('characterID');

        $character = CharacterProfileRuntimeCache::getInstance()->getObject($this->parameters['characterID']);
        if ($character === null) {
            throw new IllegalLinkException();
        }
    }

    /**
     * Validates parameters to serach item.
     */
    public function validateSearchItem(): void
    {
        $this->readString('itemName');
        $this->readInteger('pointAccountID');
        $this->readInteger('characterID');
    }
}
