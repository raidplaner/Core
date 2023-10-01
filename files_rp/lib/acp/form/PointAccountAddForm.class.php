<?php

namespace rp\acp\form;

use rp\data\point\account\PointAccountAction;
use rp\data\point\account\PointAccountList;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\ShowOrderFormField;
use wcf\system\form\builder\field\TextFormField;


/**
 * Shows the point account add form.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class PointAccountAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.point.account.add';

    /**
     * @inheritDoc
     */
    public $neededModules = ['RP_ITEM_ACCOUNT_EASYMODE_DISABLED'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canManagePointAccount'];

    /**
     * @inheritDoc
     */
    public $objectActionClass = PointAccountAction::class;

    /**
     * @inheritDoc
     */
    protected function createForm(): void
    {
        parent::createForm();

        $dataContainer = FormContainer::create('data')
            ->label('wcf.global.form.data')
            ->appendChildren([
            TextFormField::create('pointAccountName')
            ->label('wcf.global.name')
            ->autoFocus()
            ->required()
            ->maximumLength(255)
            ->i18n()
            ->languageItemPattern('rp.acp.point.account.account\d+'),
            TextFormField::create('description')
            ->label('wcf.global.description')
            ->autoFocus()
            ->maximumLength(255)
            ->i18n()
            ->languageItemPattern('rp.acp.point.account.account\d+.description'),
            ShowOrderFormField::create()
            ->description('rp.acp.point.account.showOrder.description')
            ->required()
            ->options(new PointAccountList()),
        ]);
        $this->form->appendChild($dataContainer);
    }
}
