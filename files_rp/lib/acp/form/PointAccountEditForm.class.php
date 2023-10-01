<?php

namespace rp\acp\form;

use rp\data\point\account\PointAccount;
use wcf\system\exception\IllegalLinkException;


/**
 * Shows the point account edit form.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class PointAccountEditForm extends PointAccountAddForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.point.account.list';

    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (isset($_REQUEST['id'])) {
            $this->formObject = new PointAccount($_REQUEST['id']);
            if (!$this->formObject->pointAccountID) {
                throw new IllegalLinkException();
            }
        }
    }
}
