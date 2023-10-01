<?php

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
 * @author      Marco Daries
 * @package     Daries\RP
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
