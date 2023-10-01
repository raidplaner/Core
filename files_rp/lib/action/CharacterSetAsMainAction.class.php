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
 * Sets the current character to Main.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Action
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
