/**
 * Drag and Drop attendee box's.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */

import * as Ajax from "WoltLabSuite/Core/Ajax";
import  { Autobind } from "./Autobind";
import * as Core from "WoltLabSuite/Core/Core";
import { DragTarget } from "./Data";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

class DragAndDropBox implements DragTarget {
    private readonly element: HTMLElement;
    
    constructor(element: HTMLElement) {
        this.element = element;
        
        this.configure();
    }
    
    protected configure(): void {
        this.element.addEventListener('dragover', (event) => this.dragOverHandler(event));
        this.element.addEventListener('drop', (event) => this.dropHandler(event));
        this.element.addEventListener('dragleave', (event) => this.dragLeaveHandler(event));
    }
    
    @Autobind
    public dragOverHandler(event: DragEvent): void {
        if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move") return; 
        event.preventDefault();
        
        const droppable = this.element.dataset.droppable!;
        const droppableTo = event.dataTransfer.getData("droppableTo");
        if (droppableTo.indexOf(droppable) < 0) return;
        
        this.element.classList.add("selected");
    }
    
    @Autobind
    public dropHandler(event: DragEvent): void {
        if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move") return; 
        event.preventDefault();
        
        const droppable = this.element.dataset.droppable!;
        const droppableTo = event.dataTransfer.getData("droppableTo");
        if (droppableTo.indexOf(droppable) < 0) return;
        
        const status = this.element.dataset.status;
        const distributionId = this.element.dataset.objectId;
        
        if (status === event.dataTransfer.getData("currentStatus") &&
            distributionId === event.dataTransfer.getData("distributionID")) return
        
        const attendeeId = ~~event.dataTransfer.getData("attendeeID");
        Ajax.apiOnce({
            data: {
                actionName: "updateStatus",
                className: "rp\\data\\event\\raid\\attendee\\EventRaidAttendeeAction",
                objectIDs: [ attendeeId ],
                parameters: {
                    distributionID: distributionId,
                    status: status,
                }
            },
            success: () => {
                const attendeeList = this.element.querySelector(".attendeeList") as HTMLElement;
                const attendee = document.getElementById(event.dataTransfer!.getData("id")) as HTMLElement;
                attendeeList.insertAdjacentElement("beforeend", attendee);
                
                UiNotification.show();
            },
        });
    }
    
    @Autobind
    public dragLeaveHandler(event: DragEvent): void {
        if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move") return; 
        event.preventDefault();
        
        this.element.classList.remove("selected");
    }
}

Core.enableLegacyInheritance(DragAndDropBox);

export = DragAndDropBox;