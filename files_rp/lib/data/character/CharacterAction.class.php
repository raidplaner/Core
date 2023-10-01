<?php

namespace rp\data\character;

use rp\data\character\avatar\CharacterAvatar;
use rp\data\character\avatar\CharacterAvatarAction;
use rp\data\rank\RankCache;
use rp\system\cache\runtime\CharacterRuntimeCache;
use rp\system\character\CharacterHandler;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IClipboardAction;
use wcf\data\ISearchAction;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\event\EventHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\file\upload\UploadFile;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\request\RequestHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;
use wcf\util\ImageUtil;


/**
 * Executes character related actions.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method      CharacterEditor[]   getObjects()
 * @method      CharacterEditor     getSingleObject()
 */
class CharacterAction extends AbstractDatabaseObjectAction implements IClipboardAction, ISearchAction
{
    /**
     * @inheritDoc
     */
    protected $allowGuestAccess = ['getSearchResultList'];

    /**
     * chracter object
     */
    protected ?Character $character = null;

    /**
     * @inheritDoc
     */
    protected $className = CharacterEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.rp.canAddCharacter'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.rp.canDeleteCharacter'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.rp.canEditCharacter'];

    /**
     * @inheritDoc
     */
    public function create(): Character
    {
        if (!empty($this->parameters['notes_htmlInputProcessor'])) {
            $this->parameters['data']['notes'] = $this->parameters['notes_htmlInputProcessor']->getHtml();
        }

        if (!isset($this->parameters['data']['rankID'])) {
            $this->parameters['data']['rankID'] = (RankCache::getInstance()->getDefaultRank($this->parameters['data']['gameID']))->rankID;
        }

        $this->parameters['data']['created'] = $this->parameters['data']['lastUpdateTime'] = TIME_NOW;

        if ($this->parameters['data']['userID'] !== null) {
            if (RequestHandler::getInstance()->isACPRequest()) {
                $characterList = new CharacterList();
                $characterList->getConditionBuilder()->add('userID = ?', [$this->parameters['data']['userID']]);
                $characterList->getConditionBuilder()->add('gameID = ?', [RP_DEFAULT_GAME_ID]);
                $characterList->getConditionBuilder()->add('isPrimary = ?', [1]);
                $this->parameters['data']['isPrimary'] = \intval(($characterList->countObjects() === 0));
            } else {
                $this->parameters['data']['isPrimary'] = \intval((CharacterHandler::getInstance()->getPrimaryCharacter() === null));
            }
        } else {
            $this->parameters['data']['isPrimary'] = 1;
            $this->parameters['data']['isDisabled'] = 1;
        }

        /** @var Character $character */
        $character = parent::create();

        $updates = [];
        // save embedded objects
        if (!empty($this->parameters['notes_htmlInputProcessor'])) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->parameters['notes_htmlInputProcessor']->setObjectID($character->characterID);
            if (MessageEmbeddedObjectManager::getInstance()->registerObjects($this->parameters['notes_htmlInputProcessor'])) {
                $updates['notesHasEmbeddedObjects'] = 1;
            }
        }

        if (!empty($updates)) {
            $editor = new CharacterEditor($character);
            $editor->update($updates);
        }

        // image
        if (isset($this->parameters['avatarFile']) && \is_array($this->parameters['avatarFile']) && !empty($this->parameters['avatarFile'])) {
            $avatarFile = \reset($this->parameters['avatarFile']);
            $this->uploadAvatar($avatarFile, $character);
        }

        if ($character->userID) {
            UserStorageHandler::getInstance()->reset([$character->userID], 'characterPrimaryIDs');
        }

        return $character;
    }

    /**
     * @inheritDoc
     */
    public function delete(): array
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        // delete avatars
        $avatarIDs = $characterIDs = $userIDs = [];
        foreach ($this->getObjects() as $character) {
            $characterIDs[] = $character->characterID;

            if ($character->avatarID) {
                $avatarIDs[] = $character->avatarID;
            }

            if ($character->userID) {
                $userIDs[] = $character->userID;
            }
        }

        if (!empty($avatarIDs)) {
            $action = new CharacterAvatarAction($avatarIDs, 'delete');
            $action->executeAction();
        }

        if (!empty($characterIDs)) {
            // delete embedded object references
            MessageEmbeddedObjectManager::getInstance()->removeObjects('dev.daries.rp.character.notes', $characterIDs);
        }

        if (!empty($userIDs)) {
            UserStorageHandler::getInstance()->reset($userIDs, 'characterPrimaryIDs');
        }

        $this->unmarkItems();

        parent::delete();

        return ['objectIDs' => $this->objectIDs];
    }

    /**
     * delete own character
     */
    public function deleteOwnCharacter(): void
    {
        $editor = new CharacterEditor($this->character);
        $editor->update([
            'isDisabled' => 1,
            'userID' => null,
        ]);
    }

    /**
     * Disables characters.
     */
    public function disable(): void
    {
        foreach ($this->getObjects() as $object) {
            $object->update([
                'isDisabled' => 1
            ]);
        }

        $this->unmarkItems();
    }

    /**
     * Enables characters.
     */
    public function enable(): void
    {
        foreach ($this->getObjects() as $object) {
            $object->update([
                'isDisabled' => 0
            ]);
        }

        $this->unmarkItems();
    }

    /**
     * @inheritDoc
     */
    public function getSearchResultList(): array
    {
        $searchString = $this->parameters['data']['searchString'];
        $excludedSearchValues = [];
        if (isset($this->parameters['data']['excludedSearchValues'])) {
            $excludedSearchValues = $this->parameters['data']['excludedSearchValues'];
        }
        $list = [];

        // find characters
        $searchString = \addcslashes($searchString, '_%');
        $parameters = [
            'searchString' => $searchString,
        ];
        EventHandler::getInstance()->fireAction($this, 'beforeFindCharacters', $parameters);

        $characterProfileList = new CharacterProfileList();
        $characterProfileList->getConditionBuilder()->add("characterName LIKE ?", [$parameters['searchString'] . '%']);
        if (!empty($excludedSearchValues)) {
            $characterProfileList->getConditionBuilder()->add("characterName NOT IN (?)", [$excludedSearchValues]);
        }
        $characterProfileList->sqlLimit = 10;
        $characterProfileList->readObjects();

        foreach ($characterProfileList as $characterProfile) {
            $list[] = [
                'icon' => $characterProfile->getAvatar()->getImageTag(16),
                'label' => $characterProfile->characterName,
                'objectID' => $characterProfile->characterID,
            ];
        }

        return $list;
    }

    /**
     * Sets the current character to Main.
     */
    public function setAsMain(): void
    {
        $updateSQL = "  UPDATE  rp" . WCF_N . "_member
                        SET     isPrimary = ?
                        WHERE   characterID = ?";
        $updateStatement = WCF::getDB()->prepare($updateSQL);

        $sql = "SELECT  characterID, isPrimary
                FROM    rp" . WCF_N . "_member
                WHERE   userID = ?";
        $selectStatement = WCF::getDB()->prepare($sql);

        $userIDs = [];
        foreach ($this->getObjects() as $object) {
            $selectStatement->execute([$object->userID]);

            while ($row = $selectStatement->fetchArray()) {
                if ($row['isPrimary']) {
                    $updateStatement->execute([0, $row['characterID']]);
                } elseif ($row['characterID'] === $object->characterID) {
                    $updateStatement->execute([1, $row['characterID']]);
                }
            }

            $userIDs[] = $object->userID;
        }

        if (!empty($userIDs)) {
            UserStorageHandler::getInstance()->reset($userIDs, 'characterPrimaryIDs');
        }
    }

    /**
     * @inheritDoc
     */
    public function unmarkAll(): void
    {
        ClipboardHandler::getInstance()->removeItems(ClipboardHandler::getInstance()->getObjectTypeID('dev.daries.rp.character'));
    }

    /**
     * Unmarks users.
     *
     * @param int[] $characterIDs
     */
    protected function unmarkItems(array $characterIDs = []): void
    {
        if (empty($characterIDs)) {
            $characterIDs = $this->objectIDs;
        }

        if (!empty($characterIDs)) {
            ClipboardHandler::getInstance()->unmark(
                $characterIDs,
                ClipboardHandler::getInstance()->getObjectTypeID('dev.daries.rp.character')
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->parameters['data']['lastUpdateTime'] = TIME_NOW;

        if (!empty($this->parameters['notes_htmlInputProcessor'])) {
            $this->parameters['data']['notes'] = $this->parameters['notes_htmlInputProcessor']->getHtml();
        }

        if (isset($this->parameters['data']) || isset($this->parameters['counters'])) {
            if ($this->parameters['data']['userID'] === null) {
                $this->parameters['data']['isDisabled'] = 1;
            }

            parent::update();
        } else {
            if (empty($this->objects)) {
                $this->readObjects();
            }
        }

        foreach ($this->getObjects() as $object) {
            // save embedded objects
            if (!empty($this->parameters['notes_htmlInputProcessor'])) {
                /** @noinspection PhpUndefinedMethodInspection */
                $this->parameters['notes_htmlInputProcessor']->setObjectID($object->characterID);
                if ($object->notesHasEmbeddedObjects != MessageEmbeddedObjectManager::getInstance()->registerObjects($this->parameters['notes_htmlInputProcessor'])) {
                    $object->update(['notesHasEmbeddedObjects' => $object->notesHasEmbeddedObjects ? 0 : 1]);
                }
            }

            if (isset($this->parameters['avatarFile_removedFiles']) && \is_array($this->parameters['avatarFile_removedFiles']) && !empty($this->parameters['avatarFile_removedFiles']) && empty($this->parameters['avatarFile'])) {
                $avatarAction = new CharacterAvatarAction([$object->avatarID], 'delete');
                $avatarAction->executeAction();

                $object->update(['avatarID' => null]);
            }

            // image
            if (isset($this->parameters['avatarFile']) && \is_array($this->parameters['avatarFile']) && !empty($this->parameters['avatarFile'])) {
                $avatarFile = \reset($this->parameters['avatarFile']);
                $this->uploadAvatar($avatarFile, $object->getDecoratedObject());
            }
        }
    }

    /**
     * Uploads an avatar of the character.
     */
    protected function uploadAvatar(UploadFile $avatarFile, Character $character): void
    {
        // save new image
        if (!$avatarFile->isProcessed()) {
            // rotate avatar if necessary
            $fileLocation = ImageUtil::fixOrientation($avatarFile->getLocation());

            // shrink avatar if necessary
            try {
                $fileLocation = ImageUtil::enforceDimensions(
                        $fileLocation,
                        CharacterAvatar::AVATAR_SIZE,
                        CharacterAvatar::AVATAR_SIZE,
                        false
                );
            } catch (SystemException $e) {
                
            }

            $extension = '';
            if (($position = \mb_strrpos($avatarFile->getFilename(), '.')) !== false) {
                $extension = \mb_strtolower(\mb_substr($avatarFile->getFilename(), $position + 1));
            }

            try {
                $returnValues = (new CharacterProfileAction([$character->characterID], 'setAvatar', [
                        'fileLocation' => $fileLocation,
                        'filename' => $avatarFile->getFilename(),
                        'extension' => $extension,
                        ]))->executeAction();

                $avatar = $returnValues['returnValues']['avatar'];
                $avatarFile->setProcessed($avatar->getLocation());
            } catch (\RuntimeException $e) {
                
            }
        }
    }

    /**
     * validate `deleteOwnCharacter` function
     */
    public function validateDeleteOwnCharacter(): void
    {
        if (\count($this->objectIDs) != 1) {
            throw new UserInputException('objectIDs');
        }

        $characterID = \reset($this->objectIDs);
        $this->character = CharacterRuntimeCache::getInstance()->getObject($characterID);
        if ($this->character === null) {
            throw new UserInputException('objectIDs');
        }

        if (!$this->character->canDelete()) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * Validates the disable action.
     */
    public function validateDisable()
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        foreach ($this->getObjects() as $object) {
            if ($object->isDisabled) {
                throw new UserInputException('objectIDs');
            }
        }

        if (!RequestHandler::getInstance()->isACPRequest()) {
            WCF::getSession()->checkPermissions(['user.rp.canDeleteOwnCharacter']);
        }
    }

    /**
     * Validates the `enable` action.
     */
    public function validateEnable(): void
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        foreach ($this->getObjects() as $object) {
            if (!$object->isDisabled) {
                throw new UserInputException('objectIDs');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function validateGetSearchResultList(): void
    {
        $this->readString('searchString', false, 'data');

        if (isset($this->parameters['data']['excludedSearchValues']) && !\is_array($this->parameters['data']['excludedSearchValues'])) {
            throw new UserInputException('excludedSearchValues');
        }
    }

    /**
     * Validates the `setAsMain` action.
     */
    public function validateSetAsMain(): void
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        foreach ($this->getObjects() as $object) {
            if (!$object->userID) {
                throw new UserInputException('objectIDs');
            }

            if (!$object->canEdit()) {
                throw new UserInputException('objectIDs');
            }

            if ($object->isDisabled) {
                throw new UserInputException('objectIDs');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function validateUnmarkAll(): void
    {
        // does nothing
    }
}
