<?php

namespace rp\form;


/**
 * Shows the character add form.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class CharacterAddForm extends \rp\acp\form\CharacterAddForm
{
    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.rp.canAddCharacter'];

    /**
     * @inheritDoc
     */
    public $objectEditLinkApplication = 'rp';

    /**
     * @inheritDoc
     */
    public $objectEditLinkController = CharacterEditForm::class;

}
