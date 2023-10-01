<?php

namespace rp\system\event;

use rp\system\form\builder\field\DateFormField;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\wysiwyg\WysiwygFormField;
use wcf\system\form\builder\IFormDocument;


/**
 * Default event implementation for event controllers.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class DefaultEventController extends AbstractEventController
{
    /**
     * @inheritDoc
     */
    protected string $eventNodesPosition = 'center';
    /**
     * @inheritDoc
     */
    protected string $objectTypeName = 'dev.daries.rp.event.default';

    /**
     * @inheritDoc
     */
    protected array $savedFields = [
        'enableComments',
        'endTime',
        'isFullDay',
        'notes',
        'startTime',
        'title',
        'userID',
        'username'
    ];

    /**
     * @inheritDoc
     */
    public function createForm(IFormDocument $form): void
    {
        $dataContainer = FormContainer::create('data')
            ->label('wcf.global.form.data')
            ->appendChild(
            TitleFormField::create()
            ->required()
            ->maximumLength(255)
        );
        $form->appendChild($dataContainer);

        $this->formEventDate($dataContainer, true);

        $dataContainer->appendChild(
            WysiwygFormField::create('notes')
                ->label('rp.event.notes')
                ->objectType('dev.daries.rp.event.notes')
        );
        
        $this->formComment($dataContainer);
        $this->formLegends($form);

        parent::createForm($form);
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getModerationTemplate(): string
    {
        return 'moderationEventDefault';
    }
}
