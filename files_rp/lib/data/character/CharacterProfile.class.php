<?php

namespace rp\data\character;

use rp\data\character\avatar\CharacterAvatar;
use rp\data\character\avatar\CharacterAvatarDecorator;
use rp\data\character\avatar\DefaultCharacterAvatar;
use rp\data\character\avatar\ICharacterAvatar;
use rp\data\game\Game;
use rp\data\game\GameCache;
use rp\data\rank\Rank;
use rp\data\rank\RankCache;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\ITitledLinkObject;
use wcf\system\event\EventHandler;
use wcf\system\exception\ImplementationException;
use wcf\system\user\storage\UserStorageHandler;
use wcf\util\StringUtil;


/**
 * Decorates the character object and provides functions to retrieve data for character profiles.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method      Character       getDecoratedObject()
 * @mixin       Character
 */
class CharacterProfile extends DatabaseObjectDecorator implements ITitledLinkObject
{
    /**
     * character avatar
     */
    protected ?CharacterAvatarDecorator $avatar = null;

    /**
     * @inheritDoc
     */
    protected static $baseClass = Character::class;

    /**
     * rank object
     */
    protected ?Rank $rank = null;

    /**
     * Returns a HTML anchor link pointing to the decorated character.
     */
    public function getAnchorTag(): string
    {
        return '<a href="' . $this->getLink() . '" class="rpCharacterLink" data-object-id="' . $this->getObjectID() . '">' . StringUtil::encodeHTML($this->getTitle()) . '</a>';
    }

    /**
     * Returns the character's avatar.
     */
    public function getAvatar(): CharacterAvatarDecorator
    {
        if ($this->avatar === null) {
            $avatar = null;

            if ($this->avatarID) {
                if (!$this->fileHash) {
                    $avatars = [];

                    if ($this->userID) {
                        $data = UserStorageHandler::getInstance()->getField('charactersAvatar', $this->userID);
                        if ($data !== null) {
                            $avatars = \unserialize($data);
                        }

                        if (isset($avatars[$this->characterID])) {
                            $avatar = $avatars[$this->characterID];
                        } else {
                            $avatar = new CharacterAvatar($this->avatarID);

                            $avatars[$this->characterID] = $avatar;
                            UserStorageHandler::getInstance()->update(
                                $this->userID,
                                'charactersAvatar',
                                \serialize($avatars)
                            );
                        }
                    } else {
                        $avatar = new CharacterAvatar($this->avatarID);
                    }
                } else {
                    $avatar = new CharacterAvatar(null, $this->getDecoratedObject()->data);
                }
            } else {
                $parameters = ['avatar' => null];
                EventHandler::getInstance()->fireAction($this, 'getAvatar', $parameters);

                if ($parameters['avatar'] !== null) {
                    if (!($parameters['avatar'] instanceof ICharacterAvatar)) {
                        throw new ImplementationException(
                                \get_class($parameters['avatar']),
                                ICharacterAvatar::class
                        );
                    }

                    $avatar = $parameters['avatar'];
                }
            }

            // use default avatar
            if ($avatar === null) {
                $avatar = new DefaultCharacterAvatar($this->characterName ?: '');
            }

            $this->avatar = new CharacterAvatarDecorator($avatar);
        }

        return $this->avatar;
    }

    /**
     * Returns the character profil with the given character name.
     */
    public static function getCharacterProfilByCharactername(string $name): CharacterProfile
    {
        $character = Character::getCharacterByCharactername($name);
        return new CharacterProfile($character);
    }

    /**
     * Returns a formatted title based on the rank.
     */
    public function getFormatedTitle(): string
    {
        if ($this->getRank() === null) return $this->getTitle();
        return $this->getRank()->prefix . $this->getTitle() . $this->getRank()->suffix;
    }

    /**
     * Returns game object.
     */
    public function getGame(): Game
    {
        return GameCache::getInstance()->getGameByID($this->gameID);
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return $this->getDecoratedObject()->getLink();
    }

    /**
     * Returns rank object.
     */
    public function getRank(): ?Rank
    {
        if ($this->rank === null && $this->rankID) {
            $this->rank = RankCache::getInstance()->getRankByID($this->rankID);
        }

        return $this->rank;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->getDecoratedObject()->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getDecoratedObject()->__toString();
    }
}
