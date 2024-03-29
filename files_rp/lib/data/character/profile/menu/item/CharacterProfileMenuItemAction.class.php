<?php

namespace rp\data\character\profile\menu\item;

use rp\system\cache\builder\CharacterProfileMenuCacheBuilder;
use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use rp\system\menu\character\profile\CharacterProfileMenu;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\ISortableAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\ArrayUtil;


/**
 * Executes character profile menu item-related actions.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      CharacterProfileMenuItem            create()
 * @method      CharacterProfileMenuItemEditor[]    getObjects()
 * @method      CharacterProfileMenuItemEditor      getSingleObject()
 */
class CharacterProfileMenuItemAction extends AbstractDatabaseObjectAction implements ISortableAction
{
    /**
     * @inheritDoc
     */
    protected $allowGuestAccess = ['getContent'];

    /**
     * menu item
     */
    protected ?CharacterProfileMenuItem $menuItem = null;

    /**
     * @inheritDoc
     */
    protected $requireACP = ['updatePosition'];

    /**
     * Returns content for given menu item.
     */
    public function getContent(): array
    {
        $contentManager = $this->menuItem->getContentManager();

        return [
            'containerID' => $this->parameters['containerID'],
            'template' => $contentManager->getContent($this->parameters['characterID']),
        ];
    }

    /**
     * @inheritDoc
     */
    public function updatePosition(): void
    {
        $sql = "UPDATE  rp" . WCF_N . "_member_profile_menu_item
                SET     showOrder = ?
                WHERE   menuItemID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);

        WCF::getDB()->beginTransaction();
        for ($i = 0, $length = \count($this->parameters['data']['structure'][0]); $i < $length; $i++) {
            $statement->execute([
                $i,
                $this->parameters['data']['structure'][0][$i],
            ]);
        }
        WCF::getDB()->commitTransaction();

        // reset cache
        CharacterProfileMenuCacheBuilder::getInstance()->reset();
    }

    /**
     * Validates menu item.
     */
    public function validateGetContent(): void
    {
        $this->readInteger('characterID');
        $this->readString('containerID');
        $this->readString('menuItem');

        $this->menuItem = CharacterProfileMenu::getInstance()->getMenuItem($this->parameters['menuItem']);
        if ($this->menuItem === null) {
            throw new UserInputException('menuItem');
        }
        if (!$this->menuItem->getContentManager()->isVisible($this->parameters['characterID'])) {
            throw new PermissionDeniedException();
        }

        $character = CharacterProfileRuntimeCache::getInstance()->getObject($this->parameters['characterID']);
        if ($character === null) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    public function validateUpdatePosition(): void
    {
        WCF::getSession()->checkPermissions(['admin.user.canManageUserOption']);

        if (!isset($this->parameters['data']['structure'][0])) {
            throw new UserInputException('structure');
        }

        $sql = "SELECT  menuItemID
                FROM    rp" . WCF_N . "_member_profile_menu_item";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        $menuItemIDs = [];
        while ($menuItemID = $statement->fetchColumn()) {
            $menuItemIDs[$menuItemID] = $menuItemID;
        }

        $this->parameters['data']['structure'][0] = ArrayUtil::toIntegerArray($this->parameters['data']['structure'][0]);
        foreach ($this->parameters['data']['structure'][0] as $menuItemID) {
            if (!isset($menuItemIDs[$menuItemID])) {
                throw new UserInputException('structure');
            }

            unset($menuItemIDs[$menuItemID]);
        }

        if (!empty($menuItemIDs)) {
            throw new UserInputException('structure');
        }
    }
}
