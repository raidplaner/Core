<?php

namespace rp\system\event;

use rp\data\character\Character;
use rp\data\character\CharacterList;
use rp\data\classification\Classification;
use rp\data\classification\ClassificationCache;
use rp\data\event\raid\attendee\EventRaidAttendee;
use rp\data\event\raid\attendee\EventRaidAttendeeList;
use rp\data\game\GameCache;
use rp\data\point\account\PointAccountCache;
use rp\data\raid\event\RaidEventCache;
use rp\data\role\Role;
use rp\data\role\RoleCache;
use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use rp\system\character\CharacterHandler;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\event\EventHandler;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\TabFormContainer;
use wcf\system\form\builder\container\TabMenuFormContainer;
use wcf\system\form\builder\field\dependency\ValueFormFieldDependency;
use wcf\system\form\builder\field\FloatFormField;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\field\MultipleSelectionFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\wysiwyg\WysiwygFormField;
use wcf\system\form\builder\IFormDocument;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 *  Project:    Raidplaner: Core
 *  Package:    dev.daries.rp
 *  Link:       http://daries.dev
 *
 *  Copyright (C) 2018-2023 Daries.dev Developer Team
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Raid event implementation for event controllers.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Event
 */
class RaidEventController extends AbstractEventController
{
    /**
     * content datas
     */
    protected ?array $contentData = null;

    /**
     * @inheritDoc
     */
    protected string $eventNodesPosition = 'right';

    /**
     * @inheritDoc
     */
    protected string $objectTypeName = 'dev.daries.rp.event.raid';

    /**
     * @inheritDoc
     */
    protected array $savedFields = [
        'enableComments',
        'endTime',
        'legendID',
        'notes',
        'startTime',
        'title',
        'userID',
        'username'
    ];

    /**
     * Creates a class distribution form container for the tab participant
     */
    protected function createClassDistribution(FormContainer $tab, SingleSelectionFormField $mode): void
    {
        $classDistributionContainer = FormContainer::create('classDistribution')
            ->label('rp.event.raid.distribution.class');

        /** @var Classification $classification */
        foreach (ClassificationCache::getInstance()->getClassifications() as $classification) {
            $classDistributionContainer->appendChild(
                IntegerFormField::create($classification->identifier)
                    ->label($classification->getTitle())
                    ->minimum(0)
                    ->maximum(99)
                    ->value(0)
            );
        }
        $classDistributionContainer->addDependency(
            ValueFormFieldDependency::create('classSelect')
                ->field($mode)
                ->values(['class'])
        );

        $tab->appendChild($classDistributionContainer);
    }

    /**
     * @inheritDoc
     */
    public function createForm(IFormDocument $form): void
    {
        $tabMenu = TabMenuFormContainer::create('raidEventTab');
        $form->appendChild($tabMenu);

        // data tab
        $dataTab = TabFormContainer::create('dataTab');
        $dataTab->label('wcf.global.form.data');
        $tabMenu->appendChild($dataTab);

        $dataContainer = FormContainer::create('data')
            ->label('wcf.global.form.data')
            ->appendChildren([
            SingleSelectionFormField::create('raidEventID')
            ->label('rp.raid.event.title')
            ->required()
            ->options(function () {
                $options = [];
                $pointAccounts = PointAccountCache::getInstance()->getPointAccounts();
                $raidEvents = RaidEventCache::getInstance()->getRaidEvents();

                foreach ($pointAccounts as $pointAccount) {
                    $options[] = [
                        'depth' => 0,
                        'isSelectable' => false,
                        'label' => $pointAccount->pointAccountName,
                        'value' => ''
                    ];

                    foreach ($raidEvents as $raidEvent) {
                        if ($raidEvent->pointAccountID === null || $raidEvent->pointAccountID != $pointAccount->pointAccountID) continue;

                        $options[] = [
                            'depth' => 1,
                            'label' => $raidEvent->eventName,
                            'value' => $raidEvent->eventID
                        ];

                        unset($raidEvents[$raidEvent->eventID]);
                    }
                }

                if (!empty($raidEvents)) {
                    foreach ($raidEvents as $raidEvent) {
                        $options[] = [
                            'depth' => 0,
                            'label' => $raidEvent->eventName,
                            'value' => $raidEvent->eventID
                        ];
                    }
                }

                return $options;
            }, true),
            FloatFormField::create('points')
            ->label('rp.event.raid.points')
            ->description('rp.event.raid.points.description')
            ->available(RP_POINTS_ENABLED)
            ->minimum(0)
            ->value(0),
            MultipleSelectionFormField::create('leaders')
            ->label('rp.event.raid.leader')
            ->filterable()
            ->options(function () {
                $characterList = new CharacterList();
                $characterList->getConditionBuilder()->add('gameID = ?', [RP_DEFAULT_GAME_ID]);
                $characterList->getConditionBuilder()->add('isDisabled = ?', [0]);
                $characterList->sqlOrderBy = 'characterName ASC';
                return $characterList;
            })
            ->addClass('eventAddLeader'),
        ]);
        $dataTab->appendChild($dataContainer);

        $this->formEventDate($dataContainer);

        $dataContainer->appendChildren([
                IntegerFormField::create('deadline')
                ->label('rp.event.raid.deadline')
                ->description('rp.event.raid.deadline.description')
                ->minimum(0)
                ->maximum(24)
                ->value(1),
                WysiwygFormField::create('notes')
                ->label('rp.event.notes')
                ->objectType('dev.daries.rp.event.notes'),
        ]);

        $this->formComment($dataContainer);
        $this->formLegends($dataTab);

        // condition tab
        $conditionTab = TabFormContainer::create('conditionTab');
        $conditionTab->label('rp.event.raid.condition');
        $tabMenu->appendChild($conditionTab);

        $conditionContainer = FormContainer::create('condition')
            ->label('rp.event.raid.condition')
            ->description('rp.event.raid.condition.description');
        $conditionTab->appendChild($conditionContainer);

        // participant tab
        $participantTab = TabFormContainer::create('participantTab');
        $participantTab->label('rp.event.raid.participants');
        $tabMenu->appendChild($participantTab);

        $distributionMode = SingleSelectionFormField::create('distributionMode')
            ->label('rp.event.raid.distribution')
            ->options(function () {
                return [
                'class' => 'rp.event.raid.distribution.class',
                'role' => 'rp.event.raid.distribution.role',
                'none' => 'rp.event.raid.distribution.none'
                ];
            })
            ->value('role');

        $participantContainer = FormContainer::create('participant')
            ->appendChildren([
            $distributionMode,
            IntegerFormField::create('participants')
            ->label('rp.event.raid.participants')
            ->minimum(0)
            ->maximum(99)
            ->value(0)
            ->addDependency(
                ValueFormFieldDependency::create('noneSelect')
                ->field($distributionMode)
                ->values(['none'])
            )
        ]);
        $participantTab->appendChild($participantContainer);

        $this->createClassDistribution($participantTab, $distributionMode);
        $this->createRoleDistribution($participantTab, $distributionMode);

        parent::createForm($form);
    }

    /**
     * Creates a role distribution form container for the tab participant
     */
    protected function createRoleDistribution(FormContainer $tab, SingleSelectionFormField $mode): void
    {
        $roleDistributionContainer = FormContainer::create('roleDistribution')
            ->label('rp.event.raid.distribution.role');

        /** @var Role $role */
        foreach (RoleCache::getInstance()->getRoles() as $role) {
            $roleDistributionContainer->appendChild(
                IntegerFormField::create($role->identifier)
                    ->label($role->getTitle())
                    ->minimum(0)
                    ->maximum(99)
                    ->value(0)
            );
        }
        $roleDistributionContainer->addDependency(
            ValueFormFieldDependency::create('roleSelect')
                ->field($mode)
                ->values(['role'])
        );

        $tab->appendChild($roleDistributionContainer);
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        WCF::getTPL()->assign($this->getContentData());

        return WCF::getTPL()->fetch('eventRaid', 'rp');
    }

    /**
     * Returns content data based on $key. If $key is null, all content data is returned.
     */
    public function getContentData(?string $key = null): mixed
    {
        if ($this->contentData === null) {
            $this->contentData = [];

            $hasAttendee = 0;

            $attendees = [];
            $attendeeList = new EventRaidAttendeeList();
            $attendeeList->getConditionBuilder()->add('eventID = ?', [$this->getEvent()->eventID]);
            $attendeeList->readObjects();
            /** @var EventRaidAttendee $attendee */
            foreach ($attendeeList as $attendee) {
                if ($attendee->getCharacter()->userID === WCF::getUser()->userID) $hasAttendee = $attendee->attendeeID;

                if (!isset($attendees[$attendee->status])) $attendees[$attendee->status] = [];

                switch ($this->getEvent()->distributionMode) {
                    case 'class':
                        if (!isset($attendees[$attendee->status][$attendee->classificationID])) $attendees[$attendee->status][$attendee->classificationID] = [];
                        $attendees[$attendee->status][$attendee->classificationID][] = $attendee;
                        break;
                    case 'none':
                        if (!isset($attendees[$attendee->status][0])) $attendees[$attendee->status][0] = [];
                        $attendees[$attendee->status][0][] = $attendee;
                        break;
                    case 'role':
                        if (!isset($attendees[$attendee->status][$attendee->roleID])) $attendees[$attendee->status][$attendee->roleID] = [];
                        $attendees[$attendee->status][$attendee->roleID][] = $attendee;
                        break;
                }
            }

            $distributions = [];
            switch ($this->getEvent()->distributionMode) {
                case 'class':
                    $distributions = ClassificationCache::getInstance()->getClassifications();
                    break;
                case 'none':
                    $distributions = [0 => WCF::getLanguage()->get('rp.event.raid.participants')];
                    break;
                case 'role':
                    $distributions = RoleCache::getInstance()->getRoles();
                    break;
            }

            // check users characters
            $parameters = [
                'characters' => CharacterHandler::getInstance()->getCharacters(),
            ];
            EventHandler::getInstance()->fireAction($this, 'availableCharacters', $parameters);
            $characters = $parameters['availableCharacters'] ?? $parameters['characters'];

            $raidStatus = [];

            if ($this->isLeader()) {
                $raidStatus[EventRaidAttendee::STATUS_CONFIRMED] = WCF::getLanguage()->get('rp.event.raid.container.confirmed');
            }

            $raidStatus = $raidStatus + [
                EventRaidAttendee::STATUS_LOGIN => WCF::getLanguage()->get('rp.event.raid.container.login'),
                EventRaidAttendee::STATUS_RESERVE => WCF::getLanguage()->get('rp.event.raid.container.reserve'),
                EventRaidAttendee::STATUS_LOGOUT => WCF::getLanguage()->get('rp.event.raid.container.logout'),
            ];

            $this->contentData = [
                'attendees' => $attendees,
                'availableDistributions' => $distributions,
                'availableRaidStatus' => [
                    EventRaidAttendee::STATUS_CONFIRMED => WCF::getLanguage()->get('rp.event.raid.container.confirmed'),
                    EventRaidAttendee::STATUS_LOGIN => WCF::getLanguage()->get('rp.event.raid.container.login'),
                    EventRaidAttendee::STATUS_RESERVE => WCF::getLanguage()->get('rp.event.raid.container.reserve'),
                    EventRaidAttendee::STATUS_LOGOUT => WCF::getLanguage()->get('rp.event.raid.container.logout'),
                ],
                'characters' => $characters,
                'hasAttendee' => $hasAttendee,
                'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('dev.daries.rp.raid.attendee')),
                'raidStatus' => $raidStatus,
            ];
        }

        if (\is_null($key)) return $this->contentData;
        return $this->contentData[$key] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getIcon(?int $size = null): string
    {
        $raidEvent = RaidEventCache::getInstance()->getRaidEventByID($this->getEvent()->raidEventID);
        if ($raidEvent === null) return parent::getIcon($size);
        return $raidEvent->getIcon($size);
    }

    /**
     * @inheritDoc
     */
    public function getIconPath(): string
    {
        $raidEvent = RaidEventCache::getInstance()->getRaidEventByID($this->getEvent()->raidEventID);
        if ($raidEvent === null) return parent::getIconPath();
        return StringUtil::encodeHTML($raidEvent->getIconPath());
    }

    /**
     * Returns all character profiles from the raid leader of the current raid event.
     * @return  Character[]
     */
    public function getLeaders(): array
    {
        return CharacterProfileRuntimeCache::getInstance()->getObjects($this->getEvent()->leaders);
    }

    /**
     * @inheritDoc
     */
    public function getModerationTemplate(): string
    {
        return 'moderationEventDefault';
    }

    /**
     * Returns an array of the required values.
     * 
     * Key is the language variable and value as integer.
     */
    public function getRequireds(): array
    {
        $requireds = [];
        $game = GameCache::getInstance()->getCurrentGame();
        switch ($this->getEvent()->distributionMode) {
            case 'class':
                foreach (ClassificationCache::getInstance()->getClassifications() as $classification) {
                    if (!$this->getEvent()->{$classification->identifier}) break;

                    $key = 'rp.classification.' . $game->identifier . '.' . $classification->identifier;
                    $value = $this->getEvent()->{$classification->identifier};
                    $requireds[$key] = $value;
                }
                break;
            case 'none':
                if (!$this->getEvent()->participants) break;
                $requireds['rp.event.raid.participants'] = $this->getEvent()->participants;
                break;
            case 'role':
                foreach (RoleCache::getInstance()->getRoles() as $role) {
                    if (!$this->getEvent()->{$role->identifier}) break;

                    $key = 'rp.role.' . $game->identifier . '.' . $role->identifier;
                    $value = $this->getEvent()->{$role->identifier};
                    $requireds[$key] = $value;
                }
                break;
        }


        return $requireds;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        $raidEvent = RaidEventCache::getInstance()->getRaidEventByID($this->getEvent()->raidEventID);
        if ($raidEvent === null) return 'Unknown';
        return $raidEvent->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function isExpired(): bool
    {
        $expired = $this->getEvent()->startTime - ((int) $this->getEvent()->deadline * 3600);
        if ($expired < TIME_NOW) return true;
        return false;
    }

    /**
     * Returns is current user is leader of this raid event.
     */
    public function isLeader(): bool
    {
        $characters = CharacterHandler::getInstance()->getCharacters();
        foreach ($characters as $character) {
            if (\in_array($character->characterID, $this->getEvent()->leaders)) return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    protected function prepareSave(array &$formData): void
    {
        parent::prepareSave($formData);

        if (isset($formData['leaders'])) {
            $formData['data']['leaders'] = $formData['leaders'];
            unset($formData['leaders']);
        } else {
            $formData['data']['leaders'] = [];
        }
    }

    /**
     * @inheritDoc
     */
    public function setFormObjectData(IFormDocument $form, array $fields = []): void
    {
        $fields = [
            'deadline',
            'distributionMode',
            'leaders',
            'points',
            'raidEventID',
        ];

        parent::setFormObjectData($form, $fields);

        /** @var Classification $classification */
        foreach (ClassificationCache::getInstance()->getClassifications() as $classification) {
            if (!$this->getEvent()->{$classification->identifier}) continue;

            /** @var IntegerFormField $classificationFormField */
            $classificationFormField = $form->getNodeById($classification->identifier);
            if ($classificationFormField !== null) {
                $classificationFormField->value($this->getEvent()->{$classification->identifier});
            }
        }

        /** @var Role $role */
        foreach (RoleCache::getInstance()->getRoles() as $role) {
            if (!$this->getEvent()->{$role->identifier}) continue;

            /** @var IntegerFormField $roleFormField */
            $roleFormField = $form->getNodeById($role->identifier);
            if ($roleFormField !== null) {
                $roleFormField->value($this->getEvent()->{$role->identifier});
            }
        }
    }
}
