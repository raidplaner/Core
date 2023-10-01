<?php

namespace rp\data\classification;

use rp\system\cache\builder\ClassificationCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;

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
 * Provides functions to edit classification.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Game\Classification
 * 
 * @method static   Classification      create(array $parameters = [])
 * @method          Classification      getDecoratedObject()
 * @mixin           Classification
 */
class ClassificationEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Classification::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        ClassificationCacheBuilder::getInstance()->reset();
    }
}
