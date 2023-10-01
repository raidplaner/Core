/**
 * Handles the 'mark as read' action for events.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */

import * as Ajax from "WoltLabSuite/Core/Ajax";
import { AjaxCallbackObject, AjaxCallbackSetup } from "WoltLabSuite/Core/Ajax/Data";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

class UiEventMarkAllAsRead implements AjaxCallbackObject {
    constructor() {
        document.querySelectorAll(".markAllAsReadButton").forEach((button) => {
            button.addEventListener("click", this.click.bind(this));
        });
    }

    private click(event: MouseEvent): void {
        event.preventDefault();

        Ajax.api(this);
    }

    _ajaxSuccess(): void {
        /* remove obsolete badges */
        // main menu
        document.querySelectorAll(".mainMenu .active .badge").forEach((badge) => badge.remove());
        // mobile page menu badge
        document.querySelectorAll(".pageMainMenuMobile .active").forEach((container) => {
            container.closest(".menuOverlayItem")?.querySelector(".badge")?.remove();
        });
        
        // event list
        document.querySelectorAll(".rpEvent.isNew").forEach((el) => el.classList.remove("isNew"));

        EventHandler.fire("com.woltlab.wcf.MainMenuMobile", "updateButtonState");
        UiNotification.show();
    }

    _ajaxSetup(): ReturnType<AjaxCallbackSetup> {
        return {
            data: {
                actionName: "markAllAsRead",
                className: "rp\\data\\event\\EventAction",
            },
        };
    }
}

export function init(): void {
    new UiEventMarkAllAsRead();
}