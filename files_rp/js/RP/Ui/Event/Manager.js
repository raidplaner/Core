define(["require", "exports", "tslib", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Event/Handler", "WoltLabSuite/Core/Ui/Message/Manager"], function (require, exports, tslib_1, Core, EventHandler, Manager_1) {
    "use strict";
    Core = tslib_1.__importStar(Core);
    EventHandler = tslib_1.__importStar(EventHandler);
    Manager_1 = tslib_1.__importDefault(Manager_1);
    class UiEventManager extends Manager_1.default {
        constructor(eventId) {
            super({
                className: "rp\\data\\event\\EventAction",
                selector: ".rpEventHeader",
            });
            this.eventId = eventId;
        }
        _ajaxSuccess(_data) {
            EventHandler.fire("Daries/RP/Ui/Event/Manager", "_ajaxSuccess", _data);
        }
    }
    Core.enableLegacyInheritance(UiEventManager);
    return UiEventManager;
});
