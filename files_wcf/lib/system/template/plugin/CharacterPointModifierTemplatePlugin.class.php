<?php

namespace wcf\system\template\plugin;

use rp\util\RPUtil;
use wcf\system\template\TemplateEngine;

/**
 * Template modifier plugin which formats a character point.
 *
 * Usage:
 *  {$intOrFloat|characterPoint}
 *  {123456789|characterPoint}
 * 
 * @author  Marco Daries
 * @package     WoltLabSuite\Core\System\Template\Plugin
 */
class CharacterPointModifierTemplatePlugin implements IModifierTemplatePlugin
{

    /**
     * @inheritDoc
     */
    public function execute($tagArgs, TemplateEngine $tplObj)
    {
        return RPUtil::formatPoints($tagArgs[0]);
    }
}
