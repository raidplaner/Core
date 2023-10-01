<?php

namespace rp\data\character\avatar;


/**
 * Any displayable avatar type should implement this class.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
interface ICharacterAvatar
{

    /**
     * Returns the height of this avatar.
     */
    public function getHeight(): int;

    /**
     * Returns the html code to display this avatar.
     */
    public function getImageTag(?int $size = null): string;

    /**
     * Returns the url to this avatar.
     */
    public function getURL(?int $size = null): string;

    /**
     * Returns the width of this avatar.
     */
    public function getWidth(): int;
}
