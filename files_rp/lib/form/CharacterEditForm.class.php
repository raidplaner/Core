<?php

namespace rp\form;

use wcf\system\exception\PermissionDeniedException;


/**
 * Shows the character edit form.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class CharacterEditForm extends \rp\acp\form\CharacterEditForm
{

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (!$this->formObject->canEdit()) {
            throw new PermissionDeniedException();
        }
    }
}
