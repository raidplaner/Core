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
define(["require", "exports", "tslib", "./DragAndDrop/Box", "./DragAndDrop/Item"], function (require, exports, tslib_1, Box_1, Item_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.init = void 0;
    Box_1 = tslib_1.__importDefault(Box_1);
    Item_1 = tslib_1.__importDefault(Item_1);
    function setup() {
        document.querySelectorAll(".attendeeBox").forEach((attendeeBox) => {
            new Box_1.default(attendeeBox);
        });
        document.querySelectorAll(".attendee").forEach((attendee) => {
            new Item_1.default(attendee);
        });
    }
    /**
     * Initializes drag and drop instance.
     */
    let _didInit = false;
    function init() {
        if (!_didInit) {
            setup();
        }
        _didInit = true;
    }
    exports.init = init;
});
