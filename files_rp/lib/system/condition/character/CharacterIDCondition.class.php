<?php

namespace rp\system\condition\character;

use rp\data\character\CharacterList;
use rp\system\condition\AbstractCondition;
use wcf\data\DatabaseObjectList;
use wcf\system\exception\InvalidObjectArgument;
use wcf\system\form\builder\field\IFormField;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\IFormDocument;


/**
 * Condition implementation for the character id of a character.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class CharacterIDCondition extends AbstractCondition
{

    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, IFormDocument $form, array $conditionData = []): void
    {
        if (!($objectList instanceof CharacterList)) {
            throw new InvalidObjectArgument($objectList, CharacterList::class, 'Object list');
        }

        $value = $this->getValue($form);
        if ($value !== null && $value) {
            $objectList->getConditionBuilder()->add('member.characterID = ?', [$value]);
        }
    }

    /**
     * @inheritDoc
     */
    public function getFormField(): IFormField
    {
        return IntegerFormField::create($this->getID())
                ->label('rp.character.characterID')
                ->minimum(0)
                ->value(0);
    }

    /**
     * @inheritDoc
     */
    public function getID(): string
    {
        return 'characterID';
    }
}
