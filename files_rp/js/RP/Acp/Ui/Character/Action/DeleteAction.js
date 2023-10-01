define(["require", "exports", "tslib", "./Abstract", "./Handler/Delete"], function (require, exports, tslib_1, Abstract_1, Delete_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.DeleteAction = void 0;
    Abstract_1 = tslib_1.__importDefault(Abstract_1);
    Delete_1 = tslib_1.__importDefault(Delete_1);
    class DeleteAction extends Abstract_1.default {
        constructor(button, characterId, characterDataElement) {
            super(button, characterId, characterDataElement);
            if (typeof this.button.dataset.confirmMessage !== "string") {
                throw new Error("The button does not provide a confirmMessage.");
            }
            this.button.addEventListener("click", (event) => {
                event.preventDefault();
                const deleteHandler = new Delete_1.default([this.characterId], () => {
                    this.characterDataElement.remove();
                }, this.button.dataset.confirmMessage);
                deleteHandler.delete();
            });
        }
    }
    exports.DeleteAction = DeleteAction;
    exports.default = DeleteAction;
});
