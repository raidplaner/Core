<?php

namespace rp\system\condition;

use wcf\data\object\type\AbstractObjectTypeProcessor;
use wcf\system\form\builder\IFormDocument;


/**
 * Abstract implementation of a condition.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Condition
 */
abstract class AbstractCondition extends AbstractObjectTypeProcessor implements ICondition
{

    /**
     * @inheritDoc
     */
    public function getValue(IFormDocument $form): mixed
    {
        $formField = $form->getNodeById($this->getID());
        return $formField->getSaveValue();
    }

    /**
     * @inheritDoc
     */
    public function setValue(mixed $value, IFormDocument $form): void
    {
        $formField = $form->getNodeById($this->getID());
        $formField->value($value);
    }
}
