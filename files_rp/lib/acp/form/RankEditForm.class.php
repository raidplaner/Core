<?php

namespace rp\acp\form;

use rp\data\rank\Rank;
use wcf\system\exception\IllegalLinkException;


/**
 * Shows the rank edit form.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class RankEditForm extends RankAddForm
{
    /**
     * @inheritDoc
     */
    public string $activeMenuItem = 'rp.acp.menu.link.rank.list';

    /**
     * @inheritDoc
     */
    public string $formAction = 'edit';

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (isset($_REQUEST['id'])) {
            $this->formObject = new Rank($_REQUEST['id']);
            if (!$this->formObject->rankID) {
                throw new IllegalLinkException();
            }
        }
    }
}
