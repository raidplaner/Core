<?php

/**
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
// phpcs:disable PSR1.Files.SideEffects

// Constant to get relative path to the wcf-root-dir.
// This constant is already set in each package which got an own app.config.inc.php
if (!\defined('RELATIVE_RP_DIR')) {
    \define('RELATIVE_RP_DIR', '../');
}

// include config
require_once(__DIR__ . '/../app.config.inc.php');

// include WCF
require_once(RELATIVE_WCF_DIR . 'acp/global.php');
