<?php

namespace rp\system\worker;

use rp\data\character\avatar\CharacterAvatar;
use rp\data\character\avatar\CharacterAvatarEditor;
use rp\data\character\avatar\CharacterAvatarList;
use rp\data\character\CharacterEditor;
use rp\data\character\CharacterList;
use wcf\system\exception\SystemException;
use wcf\system\html\input\HtmlInputProcessor;
use wcf\system\image\ImageHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;
use wcf\system\worker\AbstractRebuildDataWorker;

/**
 * @author  Marco Daries
 * @package     Daries\RP\System\Worker
 *
 * @method      CharacterList   getObjectList()
 */
class CharacterRebuildDataWorker extends AbstractRebuildDataWorker
{
    /**
     * @inheritDoc
     */
    protected $limit = 50;

    /**
     * @inheritDoc
     */
    protected $objectListClassName = CharacterList::class;

    /**
     * @inheritDoc
     */
    public function execute()
    {
        parent::execute();

        $characterIDs = $characters = [];
        foreach ($this->getObjectList() as $character) {
            $characters[$character->characterID] = new CharacterEditor($character);
            $characterIDs[] = $character->characterID;
        }

        $htmlInputProcessor = new HtmlInputProcessor();
        WCF::getDB()->beginTransaction();
        /** @var CharacterEditor $character */
        foreach ($characters as $character) {
            if ($character->notes) {
                $htmlInputProcessor->process(
                    $character->notes,
                    'dev.daries.rp.character.notes',
                    $character->characterID,
                );

                $character->update([
                    'notes' => $htmlInputProcessor->getHtml(),
                ]);
            }
        }
        WCF::getDB()->commitTransaction();

        // update old/imported avatars
        $avatarList = new CharacterAvatarList();
        $avatarList->getConditionBuilder()->add('member_avatar.characterID IN (?)', [$characterIDs]);
        $avatarList->getConditionBuilder()->add(
            '(
                (member_avatar.width <> ? OR member_avatar.height <> ?)
                OR (member_avatar.hasWebP = ? AND member_avatar.avatarExtension <> ?)
            )',
            [
                CharacterAvatar::AVATAR_SIZE,
                CharacterAvatar::AVATAR_SIZE,
                0,
                "gif",
            ]
        );
        $avatarList->readObjects();
        $resetAvatarCache = [];
        foreach ($avatarList as $avatar) {
            $resetAvatarCache[] = $characters[$avatar->characterID]?->userID ?? null;

            $editor = new CharacterAvatarEditor($avatar);
            if (!\file_exists($avatar->getLocation()) || @\getimagesize($avatar->getLocation()) === false) {
                // delete avatars that are missing or broken
                $editor->delete();
                continue;
            }

            $width = $avatar->width;
            $height = $avatar->height;
            if ($width != $height) {
                // make avatar quadratic
                $width = $height = \min($width, $height, CharacterAvatar::AVATAR_SIZE);
                $adapter = ImageHandler::getInstance()->getAdapter();

                try {
                    $adapter->loadFile($avatar->getLocation());
                } catch (SystemException $e) {
                    // broken image
                    $editor->delete();
                    continue;
                }

                $thumbnail = $adapter->createThumbnail($width, $height, false);
                $adapter->writeImage($thumbnail, $avatar->getLocation());
                // Clear thumbnail as soon as possible to free up the memory.
                $thumbnail = null;
            }

            if ($width != CharacterAvatar::AVATAR_SIZE || $height != CharacterAvatar::AVATAR_SIZE) {
                // resize avatar
                $adapter = ImageHandler::getInstance()->getAdapter();

                try {
                    $adapter->loadFile($avatar->getLocation());
                } catch (SystemException $e) {
                    // broken image
                    $editor->delete();
                    continue;
                }

                $adapter->resize(0, 0, $width, $height, CharacterAvatar::AVATAR_SIZE, CharacterAvatar::AVATAR_SIZE);
                $adapter->writeImage($adapter->getImage(), $avatar->getLocation());
                $width = $height = CharacterAvatar::AVATAR_SIZE;
            }

            $editor->createAvatarVariant();

            $editor->update([
                'width' => $width,
                'height' => $height,
            ]);
        }

        // Reset the avatar cache for all avatars that had been processed.
        if (!empty($resetAvatarCache)) {
            UserStorageHandler::getInstance()->reset($resetAvatarCache, 'charactersAvatar');
        }
    }
}
