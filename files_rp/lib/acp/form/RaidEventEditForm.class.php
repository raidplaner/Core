<?php

namespace rp\acp\form;

use rp\data\raid\event\RaidEvent;
use wcf\system\exception\IllegalLinkException;


/**
 * Shows the raid event edit form.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class RaidEventEditForm extends RaidEventAddForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.raid.event.list';

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
            $this->formObject = new RaidEvent($_REQUEST['id']);
            if (!$this->formObject->eventID) {
                throw new IllegalLinkException();
            }
        }
    }
}
