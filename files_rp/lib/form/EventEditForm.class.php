<?php

namespace rp\form;

use rp\data\event\Event;
use wcf\data\object\type\ObjectTypeCache;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;


/**
 * Shows the event edit form.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class EventEditForm extends EventAddForm
{
    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        AbstractFormBuilderForm::readParameters();

        if (isset($_REQUEST['id'])) {
            $this->formObject = new Event($_REQUEST['id']);
            if (!$this->formObject->eventID) {
                throw new IllegalLinkException();
            }
        }

        $this->objectTypeID = $this->formObject->objectTypeID;
        $this->eventController = ObjectTypeCache::getInstance()->getObjectType($this->objectTypeID);
        $this->eventController->getProcessor()->setEvent($this->formObject);

        if (!$this->formObject->canEdit() && !$this->eventController->getProcessor()->isLeader()) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * @inheritDoc
     */
    public function saved(): void
    {
        AbstractFormBuilderForm::saved();
    }

    /**
     * @inheritDoc
     */
    protected function setFormObjectData()
    {
        parent::setFormObjectData();

        if (!empty($_POST)) {
            return;
        }

        $this->eventController->getProcessor()->setFormObjectData($this->form);
    }
}
