/**
 * Handles inline editing of events.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */

import * as Core from "WoltLabSuite/Core/Core";
import * as DomUtil from "WoltLabSuite/Core/Dom/Util";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import EventManager from "./Manager";
import * as Language from "WoltLabSuite/Core/Language";
import { DatabaseObjectActionResponse } from "WoltLabSuite/Core/Ajax/Data";
import * as UiConfirmation from "WoltLabSuite/Core/Ui/Confirmation";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

class UiEventInlineEditor {
    protected readonly _elements = new Map<string, HTMLElement>();
    protected readonly _eventId: number;
    protected readonly _eventManager: EventManager;
    
    constructor(eventId: number) {
        this._eventId = eventId;
        
        this._eventManager = new EventManager(eventId);
        
        this._show();
        
        EventHandler.add("Daries/RP/Ui/Event/Manager", "_ajaxSuccess", (data: DatabaseObjectActionResponse) => this._ajaxSuccess(data));
    }
    
    protected _click(element: HTMLElement, event: MouseEvent | null): void {
        if (event) {
            event.preventDefault();
        }
        
        const optionName = element.dataset.optionName!;
        
        if (optionName === "editLink" || optionName === 'transform') {
            window.location.href = element.dataset.link!;
        } else {
            this._execute(optionName);
        }
    }
    
    protected _execute(optionName: string): void {
        if (!this._validate(optionName)) {
            return;
        }
        
        switch(optionName) {
            case "disable":
            case "enable":
            case "restore":
                this._eventManager.update(this._eventId.toString(), optionName);
                break;
                
            case "cancel":
                UiConfirmation.show({
                    confirm: () => {
                        this._eventManager.update(this._eventId.toString(), optionName);
                    },
                    message: Language.get("rp.event.raid.cancel.confirmMessage"),
                    messageIsHtml: true,
                });
                break;
                
            case "delete":
                UiConfirmation.show({
                    confirm: () => {
                        this._eventManager.update(this._eventId.toString(), optionName);
                    },
                    message: Language.get("rp.event.delete.confirmMessage"),
                    messageIsHtml: true,
                });
                break;
                
            case "trash":
                UiConfirmation.show({
                    confirm: () => {
                        const textarea = UiConfirmation.getContentElement().querySelector("textarea") as HTMLTextAreaElement;
                        
                        this._eventManager.update(
                            this._eventId.toString(),
                            optionName,
                            {
                                data: {
                                    reason: textarea.value.trim()
                                }
                            }
                        );
                    },
                    message: Language.get("rp.event.trash.confirmMessage"),
                    messageIsHtml: true,
                    template: `
                        <div class="section">
                            <dl>
                                <dt>${Language.get("rp.event.trash.reason")}</dt>
                                <dd><textarea cols="40" rows="4"></textarea></dd>
                            </dl>
                        </div>`,
                });
                break;
        }
    }
    
    protected _show(): void {
        let hasShowElements = false;
        
        document.querySelectorAll(".jsEventDropdownItems > li").forEach((element: HTMLElement) => {
            const optionName = element.dataset.optionName;
            if (optionName) {
                if (this._validate(optionName)) {
                    DomUtil.show(element);
                    hasShowElements = true;
                } else {
                    DomUtil.hide(element);
                }
                
                if (!this._elements.get(optionName)) {
                    element.addEventListener("click", (ev) => this._click(element, ev));
                    
                    this._elements.set(
                        optionName,
                        element
                    );
                    
                    if (optionName === "editLink") {
                        const toggleButton = document.querySelector(".jsEventDropdown > .dropdownToggle") as HTMLAnchorElement;
                        toggleButton.addEventListener("dblclick", (event) => {
                            event.preventDefault();

                            if (!this._validate("editLink")) return;
                            element.click();
                        });
                    }
                }
            }
        });
        
        const eventDropdown = document.querySelector(".jsEventDropdown") as HTMLElement;
        if (!hasShowElements) {
            eventDropdown.remove();
        } else {
            DomUtil.show(eventDropdown);
        }
    }
    
    protected _ajaxSuccess(data: DatabaseObjectActionResponse): void {
        switch(data.actionName) {
            case "cancel":
                window.location.reload();
                break;
                
            case "disable":
            case "enable":
                this._eventManager.updateItems(
                    [this._eventId.toString()],
                    {
                        isDisabled: (data.actionName === "disable") ? "1" : "0"
                    }
                );
                break;
                
            case "restore":
            case "trash":
                this._eventManager.updateItems(
                    [this._eventId.toString()],
                    {
                        isDeleted: (data.actionName === "trash") ? "1" : "0"
                    }
                );
                break;
        }
        
        this._show();
        UiNotification.show();
    }
    
    protected _validate(optionName: string): boolean {
        const eventId = this._eventId.toString();
        
        switch(optionName) {
            case "cancel":
                if (!this._eventManager.getPermission(eventId, "cancelEvent")) {
                    return false;
                }
                
                if (!this._eventManager.getPropertyValue(eventId, "isCanceled", true)) {
                    return true;
                }
                break;
                
            case "delete":
                if (!this._eventManager.getPermission(eventId, "deleteEvent")) {
                    return false;
                }
                
                if (this._eventManager.getPropertyValue(eventId, "isDeleted", true)) {
                    return true;
                }
                break;
            
            case "restore":
                if (!this._eventManager.getPermission(eventId, "restoreEvent")) {
                    return false;
                }
                
                if (this._eventManager.getPropertyValue(eventId, "isDeleted", true)) {
                    return true;
                }
                break;
            
            case "trash":
                if (!this._eventManager.getPermission(eventId, "trashEvent")) {
                    return false;
                }
                
                if (!this._eventManager.getPropertyValue(eventId, "isDeleted", true)) {
                    return true;
                }
                break;
                
            case "enable":
            case "disable":
                if (!this._eventManager.getPermission(eventId, "moderateEvent")) {
                    return false;
                }
                
                if (this._eventManager.getPropertyValue(eventId, "isCanceled", true)) {
                    return false;
                }
                
                if (this._eventManager.getPropertyValue(eventId, "isDeleted", true)) {
                    return false;
                }
                
                if (this._eventManager.getPropertyValue(eventId, "isDisabled", true)) {
                    return (optionName === "enable");
                } else {
                    return (optionName === "disable");
                }
                break;
                
            case "editLink":
                if (!this._eventManager.getPermission(eventId, "editEvent")) {
                    return false;
                }
                
                return true;
                break;
                
            case "transform":
                if (!this._eventManager.getPermission(eventId, "transform")) {
                    return false;
                }
                
                return true;
                break;
        }
        
        return false;
    }
}

Core.enableLegacyInheritance(UiEventInlineEditor);

export = UiEventInlineEditor;