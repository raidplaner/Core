<?php

namespace rp\system\clipboard\action;

use rp\data\character\Character;
use rp\data\character\CharacterAction;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\clipboard\action\AbstractClipboardAction;
use wcf\system\clipboard\ClipboardEditorItem;
use wcf\system\WCF;


/**
 * Prepares clipboard editor items for character objects.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Clipboard\Action
 */
class CharacterClipboardAction extends AbstractClipboardAction
{
    /**
     * @inheritDoc
     */
    protected $actionClassActions = [
        'delete',
        'disable',
        'enable',
    ];

    /**
     * @inheritDoc
     */
    protected $supportedActions = [
        'delete',
        'disable',
        'enable',
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
            case 'delete':
                $item->addInternalData(
                    'confirmMessage',
                    WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.dev.daries.rp.character.delete.confirmMessage',
                        [
                            'count' => $item->getCount(),
                        ]
                    )
                );
                break;
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function getClassName(): string
    {
        return CharacterAction::class;
    }

    /**
     * @inheritDoc
     */
    public function getTypeName(): string
    {
        return 'dev.daries.rp.character';
    }

    /**
     * Returns the ids of the characters that can be deleted.
     *
     * @return	int[]
     */
    protected function validateDelete(): array
    {
        $objectIDs = [];

        /** @var Character $character */
        foreach ($this->objects as $character) {
            if ($character->canDelete()) {
                $objectIDs[] = $character->characterID;
            }
        }

        return $objectIDs;
    }

    /**
     * Returns the ids of the characters that can be disabled.
     *
     * @return  int[]
     */
    public function validateDisable(): array
    {
        $objectIDs = [];

        foreach ($this->objects as $character) {
            if (!$character->isDisabled) {
                $objectIDs[] = $character->characterID;
            }
        }

        return $objectIDs;
    }

    /**
     * Returns the ids of the characters that can be enabled.
     *
     * @return  int[]
     */
    public function validateEnable(): array
    {
        $objectIDs = [];

        foreach ($this->objects as $character) {
            if ($character->isDisabled && $character->userID !== null) {
                $objectIDs[] = $character->characterID;
            }
        }

        return $objectIDs;
    }
}
