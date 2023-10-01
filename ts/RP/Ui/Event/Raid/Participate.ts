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
 * Manages the participate button in the raid event.
 *
 * @author      Marco Daries
 * @module      Daries/RP/Ui/Event/Raid/Participate
 */

import * as Ajax from "WoltLabSuite/Core/Ajax";
import * as Core from "WoltLabSuite/Core/Core";
import * as DomChangeListener from "WoltLabSuite/Core/Dom/Change/Listener";
import * as DomUtil from "WoltLabSuite/Core/Dom/Util";
import FormBuilderDialog from "WoltLabSuite/Core/Form/Builder/Dialog";
import * as Language from "WoltLabSuite/Core/Language";
import { ParticipateAjaxResponse, ParticipateButtonOptions } from "./Participate/Data"
import * as UiConfirmation from "WoltLabSuite/Core/Ui/Confirmation";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";
import User from "WoltLabSuite/Core/User";

class EventRaidParticipate {
    protected readonly _addButton: HTMLElement;
    protected readonly _buttonContainer: HTMLElement;
    protected _dialog: FormBuilderDialog;
    protected readonly _eventId: number;
    protected readonly _options: ParticipateButtonOptions;
    protected readonly _removeButton: HTMLElement;

    /**
     * Initializes the event raid inline editor for attendees.
     */
    constructor(eventId: number, options: ParticipateButtonOptions) {
        this._options = Core.extend(
            {
                attendeeId: 0,
                canParticipate: false,
                hasAttendee: false,
                isExpired: false,
            }, 
            options,
        ) as ParticipateButtonOptions;
        
        if (!this._options.canParticipate) return;
        
        this._eventId = eventId;
        
        this._buttonContainer = document.querySelector(".jsButtonAttendee") as HTMLElement;
        
        // create participate buttons
        this._addButton = this._createButton(Language.get("rp.event.raid.participate"), "fa-plus");
        this._removeButton = this._createButton(Language.get("rp.event.raid.participate.remove"), "fa-trash");
        
        DomChangeListener.add("Daries/RP/Ui/Event/Raid/Participate", () => this.toogleButton());
                
        DomUtil.show(this._buttonContainer);
    }
    
    protected _click(): void {
        if (!this._options.hasAttendee) {
            if (this._dialog === undefined) {
                this._dialog = new FormBuilderDialog("addAttendeeDialog", "rp\\data\\event\\raid\\attendee\\EventRaidAttendeeAction", "createAddDialog", {
                    dialog: {
                        title: Language.get("rp.event.raid.attendee.add"),
                    },
                    actionParameters: {
                        eventID: this._eventId,
                    },
                    submitActionName: "submitAddDialog",
                    successCallback:(data: ParticipateAjaxResponse) => this._ajaxSuccess(data),
                });
            }
            
            this._dialog.open();
        } else {
            const attendee = document.getElementById(`attendee${this._options.attendeeId}`)!;
            UiConfirmation.show({
                confirm: () => {
                    Ajax.apiOnce({
                        data: {
                            actionName: "delete",
                            className: "rp\\data\\event\\raid\\attendee\\EventRaidAttendeeAction",
                            objectIDs: [ attendee.dataset.objectId! ],
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
    
    protected _createButton(title: string, icon: string): HTMLElement {
        const button = document.createElement("a");
        button.className = "button buttonPrimary";
        button.addEventListener("click", () => this._click());
        button.innerHTML = `
            <span class="icon icon16 ${icon}"></span>
            <span>${title}</span>
        `;
        
        return button;
    }
    
    protected toogleButton(): void {
        let hasAttendee = false;
        let attendeeId = 0;
        document.querySelectorAll(".attendee").forEach((attendee: HTMLElement) => {
            if (~~attendee.dataset.userId! === User.userId) {
                hasAttendee = true;
                attendeeId = ~~attendee.dataset.objectId!;
            }
        });
        
        if (hasAttendee) {
            this._buttonContainer.replaceChildren(this._removeButton);
            this._options.hasAttendee = true;
            this._options.attendeeId = attendeeId;
        } else {
            if (!this._options.isExpired) {
                this._buttonContainer.replaceChildren(this._addButton);
                this._options.hasAttendee = false;
                this._options.attendeeId = 0;
            } else {
                this._buttonContainer.remove();
            }
        }
    }
    
    protected _ajaxSuccess(data: ParticipateAjaxResponse): void {
        document.querySelectorAll(".attendeeBox").forEach((attendeeBox: HTMLElement) => {
            if (data.distributionId === ~~attendeeBox.dataset.objectId! &&
                data.status === ~~attendeeBox.dataset.status!) {
                this._options.attendeeId = data.attendeeId;
                    
                const attendeeList = attendeeBox.querySelector(".attendeeList") as HTMLElement;
                DomUtil.insertHtml(data.template, attendeeList, "append");

                DomChangeListener.trigger();
                UiNotification.show();
            }
        });
    }
}

let _didInit = false;
export function setup(eventId: number, options: ParticipateButtonOptions): void {
    if (_didInit) return;
        _didInit = true;
        
    new EventRaidParticipate(eventId, options);
}