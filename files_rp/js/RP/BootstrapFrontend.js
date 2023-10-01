define(["require", "exports", "tslib", "WoltLabSuite/Core/Controller/Popover"], function (require, exports, tslib_1, ControllerPopover) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = void 0;
    ControllerPopover = tslib_1.__importStar(ControllerPopover);
    /**
     * Initializes character profile popover.
     */
    function _initCharacterPopover() {
        ControllerPopover.init({
            className: "rpCharacterLink",
            dboAction: "rp\\data\\character\\CharacterProfileAction",
            identifier: "dev.daries.rp.character",
        });
    }
    /**
     * Initializes event popover.
     */
    function _initEventPopover() {
        ControllerPopover.init({
            className: "rpEventLink",
            dboAction: "rp\\data\\event\\EventAction",
            identifier: "dev.daries.rp.event",
        });
    }
    /**
     * Initializes event raid attendee popover.
     */
    function _initEventRaidAttendeePopover() {
        ControllerPopover.init({
            className: "rpEventRaidAttendeeLink",
            dboAction: "rp\\data\\event\\raid\\attendee\\EventRaidAttendeeAction",
            identifier: "dev.daries.rp.event.raid.attendee",
        });
    }
    /**
     * Initializes item popover.
     */
    function _initItemPopover() {
        ControllerPopover.init({
            className: "rpItemLink",
            dboAction: "rp\\data\\item\\ItemAction",
            identifier: "dev.daries.rp.item",
        });
    }
    /**
     * Bootstraps general modules and frontend exclusive ones.
     */
    function setup(options) {
        if (options.enableCharacterPopover) {
            _initCharacterPopover();
            _initEventRaidAttendeePopover();
        }
        if (options.enableEventPopover) {
            _initEventPopover();
        }
        if (options.enableItemPopover) {
            _initItemPopover();
        }
    }
    exports.setup = setup;
});
