<?php

namespace rp\data\character\avatar;


/**
 * Wraps avatars to provide compatibility layers.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class CharacterAvatarDecorator implements ICharacterAvatar, ISafeFormatAvatar
{
    private ICharacterAvatar $avatar;

    public function __construct(ICharacterAvatar $avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @inheritDoc
     */
    public function getHeight(): int
    {
        return $this->avatar->getHeight();
    }

    /**
     * @inheritDoc
     */
    public function getImageTag(?int $size = null, bool $lazyLoading = true): string
    {
        return $this->avatar->getImageTag($size, $lazyLoading);
    }

    /**
     * @inheritDoc
     */
    public function getSafeImageTag(?int $size = null): string
    {
        if ($this->avatar instanceof ISafeFormatAvatar) {
            return $this->avatar->getSafeImageTag($size);
        }

        return $this->avatar->getImageTag($size);
    }

    /**
     * @inheritDoc
     */
    public function getSafeURL(?int $size = null): string
    {
        if ($this->avatar instanceof ISafeFormatAvatar) {
            return $this->avatar->getSafeURL($size);
        }

        return $this->avatar->getURL($size);
    }

    /**
     * @inheritDoc
     */
    public function getURL(?int $size = null): string
    {
        return $this->avatar->getURL();
    }

    /**
     * @inheritDoc
     */
    public function getWidth(): int
    {
        return $this->avatar->getWidth();
    }
}
