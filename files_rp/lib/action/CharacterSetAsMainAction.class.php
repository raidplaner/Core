<?php

namespace rp\action;

use rp\data\character\Character;
use rp\data\character\CharacterAction;
use wcf\action\AbstractAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;
use wcf\util\HeaderUtil;


/**
 * Sets the current character to Main.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class CharacterSetAsMainAction extends AbstractAction
{
    /**
     * character object
     */
    public Character $charcter;

    /**
     * character id
     */
    public int $characterID = 0;

    /**
     * @inheritDoc
     */
    public function execute(): void
    {
        parent::execute();

        $action = new CharacterAction([$this->charcter], 'setAsMain');
        $action->validateAction();
        $action->executeAction();

        $this->executed();
    }

    protected function executed(): void
    {
        parent::executed();

        HeaderUtil::delayedRedirect(
            $this->charcter->getLink(),
            WCF::getLanguage()->getDynamicVariable(
                'rp.character.setAsMain.success',
                [
                    'character' => $this->charcter
                ]
            )
        );
        exit;
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (isset($_REQUEST['id'])) $this->characterID = \intval($_REQUEST['id']);
        $this->charcter = new Character($this->characterID);
        if (!$this->charcter->characterID) {
            throw new IllegalLinkException();
        }

        if (!$this->charcter->canEdit()) {
            throw new PermissionDeniedException();
        }

        if ($this->charcter->isPrimary || $this->charcter->isDisabled) {
            throw new PermissionDeniedException();
        }
    }
}
