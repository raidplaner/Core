<?php

namespace rp\data\character\avatar;


/**
 * A safe avatar supports a broadly supported fallback image format.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
interface ISafeFormatAvatar extends ICharacterAvatar
{

    /**
     * @see ICharacterAvatar::getImageTag()
     */
    public function getSafeImageTag(?int $size = null): string;

    /**
     * @see ICharacterAvatar::getURL()
     */
    public function getSafeURL(?int $size = null): string;
}
