<?php

namespace rp\system\clipboard\action;

use rp\data\event\raid\attendee\EventRaidAttendee;
use rp\data\event\raid\attendee\EventRaidAttendeeAction;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\clipboard\action\AbstractClipboardAction;
use wcf\system\clipboard\ClipboardEditorItem;
use wcf\system\WCF;


/**
 * Clipboard action implementation for raid attendees.
 * 
 * @author  Marco Daries
 * @package     Daries\RP\System\Clipboard\Action
 */
class EventRaidAttendeeClipboardAction extends AbstractClipboardAction
{
    /**
     * @inheritDoc
     */
    protected $actionClassActions = [
        'delete'
    ];

    /**
     * @inheritDoc
     */
    protected $supportedActions = [
        'updateStatus',
        'delete'
    ];

    /**
     * @inheritDoc
     */
    public function execute($objects, ClipboardAction $action): ?ClipboardEditorItem
    {
        $item = parent::execute($objects, $action);

        if ($item === null) {
            return null;
        }

        // handle actions
        switch ($action->actionName) {
            case 'updateStatus':
                $item->addInternalData('template', WCF::getTPL()->fetch('eventRaidAttendeeStatusDialog', 'rp', [
                        'statusData' => [
                            EventRaidAttendee::STATUS_CONFIRMED => WCF::getLanguage()->get('rp.event.raid.container.confirmed'),
                            EventRaidAttendee::STATUS_LOGIN => WCF::getLanguage()->get('rp.event.raid.container.login'),
                            EventRaidAttendee::STATUS_RESERVE => WCF::getLanguage()->get('rp.event.raid.container.reserve'),
                            EventRaidAttendee::STATUS_LOGOUT => WCF::getLanguage()->get('rp.event.raid.container.logout'),
                        ]
                ]));
                $item->addInternalData('objectIDs', $item->getParameters()['objectIDs']);
                break;

            case 'delete':
                $item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.dev.daries.rp.raid.attendee.delete.confirmMessage', [
                        'count' => $item->getCount()
                ]));
                break;
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function getClassName(): string
    {
        return EventRaidAttendeeAction::class;
    }

    /**
     * @inheritDoc
     */
    public function getTypeName(): string
    {
        return 'dev.daries.rp.raid.attendee';
    }

    /**
     * Returns the ids of the participants who have access to the event.
     * 
     * @return	int[]
     */
    public function validateUpdateStatus(): array
    {
        $objectIDs = [];

        /** @var EventRaidAttendee $attendee */
        foreach ($this->objects as $attendee) {
            if ($attendee->getEvent()->canEdit()) {
                $objectIDs[] = $attendee->attendeeID;
            }
        }

        return $objectIDs;
    }

    /**
     * Returns the ids of the participants who have access to the event.
     * 
     * @return	int[]
     */
    public function validateDelete(): array
    {
        $objectIDs = [];

        /** @var EventRaidAttendee $attendee */
        foreach ($this->objects as $attendee) {
            if ($attendee->getEvent()->canEdit()) {
                $objectIDs[] = $attendee->attendeeID;
            }
        }

        return $objectIDs;
    }
}
