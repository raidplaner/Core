<?php

namespace rp\system\form\builder\field\character;

use wcf\system\form\builder\field\TextFormField;


/**
 * Implementation of a form field for character name.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class CharacterNameFormField extends TextFormField
{
    /**
     * @inheritDoc
     */
    protected $templateApplication = 'rp';

    /**
     * @inheritDoc
     */
    protected $templateName = '__characterNameFormField';

}
