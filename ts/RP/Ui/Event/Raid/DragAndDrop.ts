/**
 * Drag and Drop attendee item's.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */

import DragAndDropBox from "./DragAndDrop/Box";
import DragAndDropItem from "./DragAndDrop/Item";

function setup() {
    document.querySelectorAll(".attendeeBox").forEach((attendeeBox: HTMLElement) => {
        new DragAndDropBox(attendeeBox);
    });
    
    document.querySelectorAll(".attendee").forEach((attendee: HTMLElement) => {
        new DragAndDropItem(attendee);
    });
}

/**
 * Initializes drag and drop instance.
 */
let _didInit = false;
export function init(): void {
    if (!_didInit) {
        setup();
    }
    
    _didInit = true;
}