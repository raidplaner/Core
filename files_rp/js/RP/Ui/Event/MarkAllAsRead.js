define(["require", "exports", "tslib", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Event/Handler", "WoltLabSuite/Core/Ui/Notification"], function (require, exports, tslib_1, Ajax, EventHandler, UiNotification) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.init = void 0;
    Ajax = tslib_1.__importStar(Ajax);
    EventHandler = tslib_1.__importStar(EventHandler);
    UiNotification = tslib_1.__importStar(UiNotification);
    class UiEventMarkAllAsRead {
        constructor() {
            document.querySelectorAll(".markAllAsReadButton").forEach((button) => {
                button.addEventListener("click", this.click.bind(this));
            });
        }
        click(event) {
            event.preventDefault();
            Ajax.api(this);
        }
        _ajaxSuccess() {
            /* remove obsolete badges */
            // main menu
            document.querySelectorAll(".mainMenu .active .badge").forEach((badge) => badge.remove());
            // mobile page menu badge
            document.querySelectorAll(".pageMainMenuMobile .active").forEach((container) => {
                var _a, _b;
                (_b = (_a = container.closest(".menuOverlayItem")) === null || _a === void 0 ? void 0 : _a.querySelector(".badge")) === null || _b === void 0 ? void 0 : _b.remove();
            });
            // event list
            document.querySelectorAll(".rpEvent.isNew").forEach((el) => el.classList.remove("isNew"));
            EventHandler.fire("com.woltlab.wcf.MainMenuMobile", "updateButtonState");
            UiNotification.show();
        }
        _ajaxSetup() {
            return {
                data: {
                    actionName: "markAllAsRead",
                    className: "rp\\data\\event\\EventAction",
                },
            };
        }
    }
    function init() {
        new UiEventMarkAllAsRead();
    }
    exports.init = init;
});
