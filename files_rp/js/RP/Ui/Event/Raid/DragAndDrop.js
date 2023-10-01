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
