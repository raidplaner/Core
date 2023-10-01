<?php

namespace rp\acp\form;

use rp\data\event\legend\EventLegend;
use wcf\system\exception\IllegalLinkException;


/**
 * Shows the event legend edit form.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class EventLegendEditForm extends EventLegendAddForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.event.legend.list';

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
            $this->formObject = new EventLegend($_REQUEST['id']);
            if (!$this->formObject->legendID) {
                throw new IllegalLinkException();
            }
        }
    }
}
