<?php

namespace rp\system\condition\character;

use rp\data\character\CharacterList;
use rp\system\condition\AbstractCondition;
use rp\system\form\builder\field\character\CharacterNameFormField;
use wcf\data\DatabaseObjectList;
use wcf\system\exception\InvalidObjectArgument;
use wcf\system\form\builder\field\IFormField;
use wcf\system\form\builder\IFormDocument;


/**
 * Condition implementation for the character name of a character.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Condition\Character
 */
class CharacterNameCondition extends AbstractCondition
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
        if ($value !== null && !empty($value)) {
            $objectList->getConditionBuilder()->add(
                'member.characterName LIKE ?',
                ['%' . \addcslashes($value, '_%') . '%']
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function getFormField(): IFormField
    {
        return CharacterNameFormField::create($this->getID())
                ->label('rp.character.characterName')
                ->maximumLength(255);
    }

    /**
     * @inheritDoc
     */
    public function getID(): string
    {
        return 'characterName';
    }
}
