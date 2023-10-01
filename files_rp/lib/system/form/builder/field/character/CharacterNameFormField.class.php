<?php

namespace rp\system\form\builder\field\character;

use wcf\system\form\builder\field\TextFormField;


/**
 * Implementation of a form field for character name.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Form\Builder\Field\Character
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
