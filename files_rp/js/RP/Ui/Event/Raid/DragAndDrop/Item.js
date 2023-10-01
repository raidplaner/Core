define(["require", "exports", "tslib", "./Autobind", "WoltLabSuite/Core/Core"], function (require, exports, tslib_1, Autobind_1, Core) {
    "use strict";
    Core = tslib_1.__importStar(Core);
    class DragAndDropItem {
        constructor(element) {
            this.element = element;
            this.configure();
        }
        configure() {
            this.element.addEventListener('dragstart', (event) => this.dragStartHandler(event));
            this.element.addEventListener('dragend', (event) => this.dragEndHandler(event));
        }
        dragEndHandler(_) {
            document.querySelectorAll(".attendeeBox").forEach((attendeeBox) => {
                attendeeBox.classList.remove("droppable");
                attendeeBox.classList.remove("selected");
            });
        }
        dragStartHandler(event) {
            const attendee = event.target;
            if (attendee.classList.contains("attendee")) {
                event.dataTransfer.setData("id", attendee.id);
                event.dataTransfer.setData("attendeeID", attendee.dataset.objectId);
                event.dataTransfer.setData("droppableTo", attendee.dataset.droppableTo);
                event.dataTransfer.effectAllowed = 'move';
                const currentBox = attendee.closest(".attendeeBox");
                event.dataTransfer.setData("currentStatus", currentBox.dataset.status);
                event.dataTransfer.setData("distributionID", currentBox.dataset.objectId);
                document.querySelectorAll(".attendeeBox").forEach((attendeeBox) => {
                    const droppable = attendeeBox.dataset.droppable;
                    const droppableTo = attendee.dataset.droppableTo;
                    if (droppableTo.indexOf(droppable) < 0)
                        return;
                    attendeeBox.classList.add("droppable");
                });
            }
        }
    }
    tslib_1.__decorate([
        Autobind_1.Autobind
    ], DragAndDropItem.prototype, "dragEndHandler", null);
    tslib_1.__decorate([
        Autobind_1.Autobind
    ], DragAndDropItem.prototype, "dragStartHandler", null);
    Core.enableLegacyInheritance(DragAndDropItem);
    return DragAndDropItem;
});
