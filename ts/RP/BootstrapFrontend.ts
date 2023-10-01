 /**
 * Bootstraps RP's JavaScript with additions for the frontend usage.
 *
 * @author  Marco Daries
 * @module      Daries/RP/BootstrapFrontend
 */
 
import * as ControllerPopover from "WoltLabSuite/Core/Controller/Popover";

/**
 * Initializes character profile popover.
 */
function _initCharacterPopover(): void {
    ControllerPopover.init({
        className: "rpCharacterLink",
        dboAction: "rp\\data\\character\\CharacterProfileAction",
        identifier: "dev.daries.rp.character",
    });
}

/**
 * Initializes event popover.
 */
function _initEventPopover(): void {
    ControllerPopover.init({
        className: "rpEventLink",
        dboAction: "rp\\data\\event\\EventAction",
        identifier: "dev.daries.rp.event",
    });
}

/**
 * Initializes event raid attendee popover.
 */
function _initEventRaidAttendeePopover(): void {
    ControllerPopover.init({
        className: "rpEventRaidAttendeeLink",
        dboAction: "rp\\data\\event\\raid\\attendee\\EventRaidAttendeeAction",
        identifier: "dev.daries.rp.event.raid.attendee",
    });
}

/**
 * Initializes item popover.
 */
function _initItemPopover(): void {
    ControllerPopover.init({
        className: "rpItemLink",
        dboAction: "rp\\data\\item\\ItemAction",
        identifier: "dev.daries.rp.item",
    });
}

/**
 * Bootstraps general modules and frontend exclusive ones.
 */
export function setup(options: BootstrapOptions): void {
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

interface BootstrapOptions {
    enableCharacterPopover: boolean;
    enableEventPopover: boolean;
    enableEventRaidAttendeePopover: boolean;
    enableItemPopover: boolean;
}