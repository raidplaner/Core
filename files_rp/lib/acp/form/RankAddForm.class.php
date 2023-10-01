<?php

namespace rp\acp\form;

use rp\data\rank\RankAction;
use rp\data\rank\RankCache;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\ShowOrderFormField;
use wcf\system\form\builder\field\TextFormField;


/**
 * Shows the rank add form.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class RankAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.rank.add';

    /**
     * @inheritDoc
     */
    public $neededModules = ['RP_ENABLE_RANK'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canManageRank'];

    /**
     * @inheritDoc
     */
    public $objectActionClass = RankAction::class;

    /**
     * @inheritDoc
     */
    protected function createForm(): void
    {
        parent::createForm();

        $dataContainer = FormContainer::create('data')
            ->label('wcf.global.form.data')
            ->appendChildren([
            TextFormField::create('rankName')
            ->label('wcf.global.name')
            ->required()
            ->autoFocus()
            ->maximumLength(100),
            TextFormField::create('prefix')
            ->label('rp.acp.rank.prefix')
            ->autoFocus()
            ->maximumLength(25),
            TextFormField::create('suffix')
            ->label('rp.acp.rank.suffix')
            ->autoFocus()
            ->maximumLength(25),
            ShowOrderFormField::create()
            ->description('rp.acp.rank.showOrder.description')
            ->required()
            ->options(RankCache::getInstance()->getRanks()),
        ]);
        $this->form->appendChild($dataContainer);
    }
}
