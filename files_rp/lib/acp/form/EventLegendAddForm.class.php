<?php

namespace rp\acp\form;

use rp\data\event\legend\EventLegendAction;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\ColorFormField;
use wcf\system\form\builder\field\TextFormField;


/**
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class EventLegendAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.event.legend.add';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canManageEventLegend'];

    /**
     * @inheritDoc
     */
    public $objectActionClass = EventLegendAction::class;

    /**
     * @inheritDoc
     */
    protected function createForm(): void
    {
        parent::createForm();

        $dataContainer = FormContainer::create('data')
            ->label('wcf.global.form.data')
            ->appendChildren(
            [
                TextFormField::create('name')
                ->label('wcf.global.name')
                ->autoFocus()
                ->required()
                ->maximumLength(255),
                ColorFormField::create('frontColor')
                ->label('rp.acp.event.legend.frontColor'),
                ColorFormField::create('bgColor')
                ->label('rp.acp.event.legend.bgColor'),
            ]
        );
        $this->form->appendChild($dataContainer);
    }
}
