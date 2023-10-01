/**
 *  Provides participation in events.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */

import * as Core from "WoltLabSuite/Core/Core";
import * as Ajax from "WoltLabSuite/Core/Ajax";
import { AjaxCallbackSetup, DatabaseObjectActionResponse } from "WoltLabSuite/Core/Ajax/Data"; 
import DomUtil from "WoltLabSuite/Core/Dom/Util";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

const appointments = new Map<number, AppointmentData>();

class EventAppointment {
    private acceptedButton: HTMLButtonElement;
    private canceledButton: HTMLButtonElement;
    private readonly eventId: number;
    private maybeButton: HTMLButtonElement;
    private readonly userId: number;
    
    constructor(eventId: number, userId: number) {
        this.eventId = eventId;
        this.userId = userId;
        
        this.acceptedButton = document.querySelector(".jsButtonEventAccepted") as HTMLButtonElement;
        this.acceptedButton.addEventListener("click", (ev) => this.click(ev));

        this.canceledButton = document.querySelector(".jsButtonEventCanceled") as HTMLButtonElement;
        this.canceledButton.addEventListener("click", (ev) => this.click(ev));

        this.maybeButton = document.querySelector(".jsButtonEventMaybe") as HTMLButtonElement;
        this.maybeButton.addEventListener("click", (ev) => this.click(ev));

        document.querySelectorAll(".jsEventAccepted .contentItemList > LI").forEach((appointment: HTMLLIElement) => this.initAppointment(appointment, "accepted"));
        document.querySelectorAll(".jsEventCanceled .contentItemList > LI").forEach((appointment: HTMLLIElement) => this.initAppointment(appointment, "canceled"));
        document.querySelectorAll(".jsEventMaybe .contentItemList > LI").forEach((appointment: HTMLLIElement) => this.initAppointment(appointment, "maybe"));
        
        const header = document.querySelector(".rpEventHeader") as HTMLElement;
        const disable = (header.dataset.isDeleted === "1" || header.dataset.isDisabled === "1") ? true : false;
        if (disable) {
            this.disableButton(this.acceptedButton);
            this.disableButton(this.canceledButton);
            this.disableButton(this.maybeButton);
        }
        
        EventHandler.add("Daries/RP/Ui/Event/Manager", "_ajaxSuccess", (data: DatabaseObjectActionResponse) => this.managerSuccess(data));
    }
    
    private click(event: MouseEvent): void {
        const button = event.currentTarget as HTMLButtonElement;
        const status = button.dataset.status;

        if (button.disabled) return;

        const appointment = appointments.get(this.userId);
        if (appointment && status === appointment.status) {
            return;
        }

        Ajax.api(this, {
            parameters: {
                eventID: this.eventId,
                status: status,
                userID: this.userId,
                exists: appointment ? 1 : 0
            }
        });
    }
    
    private disableButton(button: HTMLButtonElement) {
        button.classList.add("disabled");
        button.disabled = true;
    }

    private enableButton(button: HTMLButtonElement) {
        button.classList.remove("disabled");
        button.disabled = false;
    }

    private initAppointment(appointment: HTMLElement, status: string) {
        const userId = ~~appointment.dataset.objectId!;
            
        if (this.userId === userId) {
            switch (status) {
                case "accepted":
                    this.disableButton(this.acceptedButton);
                    break;
                case "canceled":
                    this.disableButton(this.canceledButton);
                    break;
                case "maybe":
                    this.disableButton(this.maybeButton);
                    break;
            }
        }

        appointments.set(userId, {
            status: status
        });
    }
    
    protected managerSuccess(data: DatabaseObjectActionResponse): void {
        let hasEvent = false;
        Array.from(data.objectIDs).forEach((objectId: number) => {
            if (objectId === this.eventId) hasEvent = true;
        });
        
        if (hasEvent) {
            switch(data.actionName) {
                case "disable":
                case "trash":
                    this.disableButton(this.acceptedButton);
                    this.disableButton(this.canceledButton);
                    this.disableButton(this.maybeButton);
                    break;
                case "enable":
                case "restore":
                    if (appointments.get(this.userId)?.status !== "accepted") {
                        this.enableButton(this.acceptedButton);
                    }
                    if (appointments.get(this.userId)?.status !== "canceled") {
                        this.enableButton(this.canceledButton);
                    }
                    if (appointments.get(this.userId)?.status !== "maybe") {
                        this.enableButton(this.maybeButton);
                    }
                    break;
            }
        }
    }

    _ajaxSetup(): ReturnType<AjaxCallbackSetup> {
        return {
            data: {
                actionName: "changeEventAppointmentStatus",
                className: "rp\\data\\event\\EventAction"
            }
        };
    }

    _ajaxSuccess(data: AjaxResponse): void {
        // remove old appointment by user id
        const appointment = appointments.get(data.returnValues.userID);
        if (appointment) {
            switch (appointment.status) {
                case "accepted":
                    this.enableButton(this.acceptedButton);
                    break;
                case "canceled":
                    this.enableButton(this.canceledButton);
                    break;
                case "maybe":
                    this.enableButton(this.maybeButton);
                    break;
            }
            
            switch (appointment.status) {
                case "accepted":
                    document.querySelectorAll(".jsEventAccepted .contentItemList > LI").forEach((appointment: HTMLLIElement) => {
                        const userId: number = ~~appointment.dataset.objectId!;
                        if (data.returnValues.userID === userId) {
                            appointment.remove();
                        }
                    });
                    break;
                case "canceled":
                    document.querySelectorAll(".jsEventCanceled .contentItemList > LI").forEach((appointment: HTMLLIElement) => {
                        const userId: number = ~~appointment.dataset.objectId!;
                        if (data.returnValues.userID === userId) {
                            appointment.remove();
                        }
                    });
                    break;
                case "maybe":
                    document.querySelectorAll(".jsEventMaybe .contentItemList > LI").forEach((appointment: HTMLLIElement) => {
                        const userId: number = ~~appointment.dataset.objectId!;
                        if (data.returnValues.userID === userId) {
                            appointment.remove();
                        }
                    });
                    break;
            }
        }

        let object: HTMLElement;
        switch (data.returnValues.status) {
            case "accepted":
                this.disableButton(this.acceptedButton);

                object = document.querySelector(".jsEventAccepted .contentItemList") as HTMLElement;
                if (object === null) {
                    document.querySelector(".jsEventAccepted .info")!.remove();
                    document.querySelector(".jsEventAccepted")!.appendChild(this._newObject());
                }
                DomUtil.insertHtml(data.returnValues.template, document.querySelector(".jsEventAccepted .contentItemList")!, "append");
                break;
            case "canceled":
                this.disableButton(this.canceledButton);

                object = document.querySelector(".jsEventCanceled .contentItemList") as HTMLElement;
                if (object === null) {
                    document.querySelector(".jsEventCanceled .info")!.remove();
                    document.querySelector(".jsEventCanceled")!.appendChild(this._newObject());
                }
                DomUtil.insertHtml(data.returnValues.template, document.querySelector(".jsEventCanceled .contentItemList")!, "append");
                break;
            case "maybe":
                this.disableButton(this.maybeButton);

                object = document.querySelector(".jsEventMaybe .contentItemList") as HTMLElement;
                if (object === null) {
                    document.querySelector(".jsEventMaybe .info")!.remove();
                    document.querySelector(".jsEventMaybe")!.appendChild(this._newObject());
                }
                DomUtil.insertHtml(data.returnValues.template, document.querySelector(".jsEventMaybe .contentItemList")!, "append");
                break;
        }

        appointments.set(data.returnValues.userID, {
            status: data.returnValues.status
        });

        UiNotification.show();
    }

    _newObject(): HTMLOListElement {
        const newObject = document.createElement("ol");
        newObject.className = "contentItemList eventAppointment";
        return newObject;
    }
}

Core.enableLegacyInheritance(EventAppointment);

export = EventAppointment;

interface AppointmentData {
    status: string;
}

interface AjaxResponse extends DatabaseObjectActionResponse {
    returnValues: {
        status: string;
        template: string;
        userID: number;
    };
}