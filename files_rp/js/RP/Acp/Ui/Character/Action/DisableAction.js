define(["require", "exports", "tslib", "./Abstract", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Event/Handler", "WoltLabSuite/Core/Ui/Notification"], function (require, exports, tslib_1, Abstract_1, Ajax, Core, EventHandler, UiNotification) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.DisableAction = void 0;
    Abstract_1 = tslib_1.__importDefault(Abstract_1);
    Ajax = tslib_1.__importStar(Ajax);
    Core = tslib_1.__importStar(Core);
    EventHandler = tslib_1.__importStar(EventHandler);
    UiNotification = tslib_1.__importStar(UiNotification);
    class DisableAction extends Abstract_1.default {
        constructor(button, characterId, characterDataElement) {
            super(button, characterId, characterDataElement);
            this.button.addEventListener("click", (event) => {
                event.preventDefault();
                const isEnabled = Core.stringToBool(this.characterDataElement.dataset.enabled);
                Ajax.api(this, {
                    actionName: isEnabled ? "disable" : "enable",
                });
            });
        }
        _ajaxSetup() {
            return {
                data: {
                    className: "rp\\data\\character\\CharacterAction",
                    objectIDs: [this.characterId],
                },
            };
        }
        _ajaxSuccess(data) {
            data.objectIDs.forEach((objectId) => {
                if (~~objectId == this.characterId) {
                    switch (data.actionName) {
                        case "enable":
                            this.characterDataElement.dataset.enabled = "true";
                            this.button.textContent = this.button.dataset.disableMessage;
                            break;
                        case "disable":
                            this.characterDataElement.dataset.enabled = "false";
                            this.button.textContent = this.button.dataset.enableMessage;
                            break;
                        default:
                            throw new Error("Unreachable");
                    }
                }
            });
            UiNotification.show();
            EventHandler.fire("dev.daries.rp.acp.character", "refresh", {
                characterIds: [this.characterId],
            });
        }
    }
    exports.DisableAction = DisableAction;
    exports.default = DisableAction;
});
