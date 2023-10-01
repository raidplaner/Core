<?php

namespace rp\acp\form;

use rp\data\character\Character;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\field\IFormField;


/**
 * Shows the character edit form.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class CharacterEditForm extends CharacterAddForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.character.list';

    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canEditCharacter'];

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (isset($_REQUEST['id'])) {
            $this->formObject = new Character($_REQUEST['id']);
            if (!$this->formObject->characterID) {
                throw new IllegalLinkException();
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function setFormObjectData(): void
    {
        parent::setFormObjectData();

        if (empty($_POST)) {
            foreach ($this->formObject->additionalData as $key => $value) {
                /** @var IFormField $field */
                $field = $this->form->getNodeById($key);
                if ($field !== null) {
                    $field->value($value);
                }
            }
        }
    }
}
