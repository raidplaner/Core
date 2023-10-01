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
define(["require", "exports", "tslib", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Ui/Confirmation"], function (require, exports, tslib_1, Ajax, UiConfirmation) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.Delete = void 0;
    Ajax = tslib_1.__importStar(Ajax);
    UiConfirmation = tslib_1.__importStar(UiConfirmation);
    class Delete {
        constructor(characterIDs, successCallback, deleteMessage) {
            this.characterIDs = characterIDs;
            this.successCallback = successCallback;
            this.deleteMessage = deleteMessage;
        }
        delete() {
            UiConfirmation.show({
                confirm: () => {
                    Ajax.apiOnce({
                        data: {
                            actionName: "delete",
                            className: "rp\\data\\character\\CharacterAction",
                            objectIDs: this.characterIDs,
                        },
                        success: this.successCallback,
                    });
                },
                message: this.deleteMessage,
                messageIsHtml: true,
            });
        }
    }
    exports.Delete = Delete;
    exports.default = Delete;
});
