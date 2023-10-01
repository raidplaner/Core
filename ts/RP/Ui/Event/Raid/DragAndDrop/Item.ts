 /**
 * It creates an item to go on the list
 *
 * @author  Marco Daries
 * @module      Daries/RP/Ui/Event/Raid/DragAndDrop/Item
 */

import { Autobind } from "./Autobind";
import * as Core from "WoltLabSuite/Core/Core";
import { Draggable } from "./Data";

class DragAndDropItem implements Draggable {
    private readonly element: HTMLElement;
    
    constructor(element: HTMLElement) {
        this.element = element;
        
        this.configure();
    }
    
    protected configure(): void {
        this.element.addEventListener('dragstart', (event) => this.dragStartHandler(event));
        this.element.addEventListener('dragend', (event) => this.dragEndHandler(event));
    }
    
    @Autobind
    dragEndHandler(_: DragEvent): void {
        document.querySelectorAll(".attendeeBox").forEach((attendeeBox: HTMLElement) => {
            attendeeBox.classList.remove("droppable");
            attendeeBox.classList.remove("selected");
        });
    }
    
    @Autobind
    dragStartHandler(event: DragEvent): void {
        const attendee = event.target as HTMLElement;
        
        if (attendee.classList.contains("attendee")) {
            event.dataTransfer!.setData("id", attendee.id);
            event.dataTransfer!.setData("attendeeID", attendee.dataset.objectId!);
            event.dataTransfer!.setData("droppableTo", attendee.dataset.droppableTo!);
            event.dataTransfer!.effectAllowed = 'move';
            
            const currentBox = attendee.closest(".attendeeBox") as HTMLElement;
            event.dataTransfer!.setData("currentStatus", currentBox.dataset.status!);
            event.dataTransfer!.setData("distributionID", currentBox.dataset.objectId!);
            
            document.querySelectorAll(".attendeeBox").forEach((attendeeBox: HTMLElement) => {
                const droppable = <string>attendeeBox.dataset.droppable;
                const droppableTo = <string>attendee.dataset.droppableTo;
                if (droppableTo.indexOf(droppable) < 0) return;
                
                attendeeBox.classList.add("droppable");
            });
        }
    }
}

Core.enableLegacyInheritance(DragAndDropItem);

export = DragAndDropItem;