<?php

namespace rp\system\form\builder\field\character;

use wcf\data\DatabaseObjectList;
use wcf\data\IObjectTreeNode;
use wcf\data\ITitledObject;
use wcf\system\form\builder\field\MultipleSelectionFormField;
use wcf\system\WCF;
use wcf\util\ClassUtil;


/**
 * Extended the Implementation of a form field for selecting multiple values for characters.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class CharacterMultipleSelectionFormField extends MultipleSelectionFormField
{
    /**
     * @inheritDoc
     */
    protected $templateApplication = 'rp';

    /**
     * @inheritDoc
     */
    protected $templateName = '__characterMultipleSelectionFormField';

    /**
     * @inheritDoc
     */
    public function options($options, $nestedOptions = false, $labelLanguageItems = true): self
    {
        parent::options($options, $nestedOptions, $labelLanguageItems);

        if ($nestedOptions) {
            foreach ($this->nestedOptions as $key => $option) {
                if (isset($options[$option['value']])) {
                    $this->nestedOptions[$key]['userID'] = $options[$option['value']]['userID'] ?? 0;
                }
            }
        }

        return $this;
    }
}
