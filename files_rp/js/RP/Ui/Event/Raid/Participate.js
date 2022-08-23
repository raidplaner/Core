/**
 *  Project:    Raidplaner: Core
 *  Package:    info.daries.rp
 *  Link:       http://daries.info
 *
 *  Copyright (C) 2018-2022 Daries.info Developer Team
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
define(["require", "exports", "tslib", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Dom/Change/Listener", "WoltLabSuite/Core/Dom/Util", "WoltLabSuite/Core/Form/Builder/Dialog", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Ui/Confirmation", "WoltLabSuite/Core/Ui/Notification", "WoltLabSuite/Core/User"], function (require, exports, tslib_1, Ajax, Core, DomChangeListener, DomUtil, Dialog_1, Language, UiConfirmation, UiNotification, User_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = void 0;
    Ajax = tslib_1.__importStar(Ajax);
    Core = tslib_1.__importStar(Core);
    DomChangeListener = tslib_1.__importStar(DomChangeListener);
    DomUtil = tslib_1.__importStar(DomUtil);
    Dialog_1 = tslib_1.__importDefault(Dialog_1);
    Language = tslib_1.__importStar(Language);
    UiConfirmation = tslib_1.__importStar(UiConfirmation);
    UiNotification = tslib_1.__importStar(UiNotification);
    User_1 = tslib_1.__importDefault(User_1);
    class EventRaidParticipate {
        /**
         * Initializes the event raid inline editor for attendees.
         */
        constructor(eventId, options) {
            this._options = Core.extend({
                attendeeId: 0,
                canParticipate: false,
                hasAttendee: false,
                isExpired: false,
            }, options);
            if (!this._options.canParticipate)
                return;
            this._eventId = eventId;
            this._buttonContainer = document.querySelector(".jsButtonAttendee");
            // create participate buttons
            this._addButton = this._createButton(Language.get("rp.event.raid.participate"), "fa-plus");
            this._removeButton = this._createButton(Language.get("rp.event.raid.participate.remove"), "fa-trash");
            DomChangeListener.add("Daries/RP/Ui/Event/Raid/Participate", () => this.toogleButton());
            DomUtil.show(this._buttonContainer);
        }
        _click() {
            if (!this._options.hasAttendee) {
                if (this._dialog === undefined) {
                    this._dialog = new Dialog_1.default("addAttendeeDialog", "rp\\data\\event\\raid\\attendee\\EventRaidAttendeeAction", "createAddDialog", {
                        dialog: {
                            title: Language.get("rp.event.raid.attendee.add"),
                        },
                        actionParameters: {
                            eventID: this._eventId,
                        },
                        submitActionName: "submitAddDialog",
                        successCallback: (data) => this._ajaxSuccess(data),
                    });
                }
                this._dialog.open();
            }
            else {
                const attendee = document.getElementById(`attendee${this._options.attendeeId}`);
                UiConfirmation.show({
                    confirm: () => {
                        Ajax.apiOnce({
                            data: {
                                actionName: "delete",
                                className: "rp\\data\\event\\raid\\attendee\\EventRaidAttendeeAction",
                                objectIDs: [attendee.dataset.objectId],
                            },
                            success: () => {
                                attendee.remove();
                                DomChangeListener.trigger();
                                UiNotification.show();
                            },
                        });
                    },
                    message: Language.get("rp.event.raid.attendee.remove.confirmMessage"),
                    messageIsHtml: true,
                });
            }
        }
        _createButton(title, icon) {
            const button = document.createElement("a");
            button.className = "button buttonPrimary";
            button.addEventListener("click", () => this._click());
            button.innerHTML = `
            <span class="icon icon16 ${icon}"></span>
            <span>${title}</span>
        `;
            return button;
        }
        toogleButton() {
            let hasAttendee = false;
            let attendeeId = 0;
            document.querySelectorAll(".attendee").forEach((attendee) => {
                if (~~attendee.dataset.userId === User_1.default.userId) {
                    hasAttendee = true;
                    attendeeId = ~~attendee.dataset.objectId;
                }
            });
            if (hasAttendee) {
                this._buttonContainer.replaceChildren(this._removeButton);
                this._options.hasAttendee = true;
                this._options.attendeeId = attendeeId;
            }
            else {
                if (!this._options.isExpired) {
                    this._buttonContainer.replaceChildren(this._addButton);
                    this._options.hasAttendee = false;
                    this._options.attendeeId = 0;
                }
                else {
                    this._buttonContainer.remove();
                }
            }
        }
        _ajaxSuccess(data) {
            document.querySelectorAll(".attendeeBox").forEach((attendeeBox) => {
                if (data.distributionId === ~~attendeeBox.dataset.objectId &&
                    data.status === ~~attendeeBox.dataset.status) {
                    this._options.attendeeId = data.attendeeId;
                    const attendeeList = attendeeBox.querySelector(".attendeeList");
                    DomUtil.insertHtml(data.template, attendeeList, "append");
                    DomChangeListener.trigger();
                    UiNotification.show();
                }
            });
        }
    }
    let _didInit = false;
    function setup(eventId, options) {
        if (_didInit)
            return;
        _didInit = true;
        new EventRaidParticipate(eventId, options);
    }
    exports.setup = setup;
});
