define(["require", "exports", "tslib", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Ui/Confirmation"], function (require, exports, tslib_1, Ajax, UiConfirmation) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.Delete = void 0;
    Ajax = tslib_1.__importStar(Ajax);
    UiConfirmation = tslib_1.__importStar(UiConfirmation);
    class Delete {
        constructor(characterIDs, successCallback, deleteMessage) {
            this.characterIDs = characterIDs;
            this.successCallback = successCallback;
            this.deleteMessage = deleteMessage;
        }
        delete() {
            UiConfirmation.show({
                confirm: () => {
                    Ajax.apiOnce({
                        data: {
                            actionName: "delete",
                            className: "rp\\data\\character\\CharacterAction",
                            objectIDs: this.characterIDs,
                        },
                        success: this.successCallback,
                    });
                },
                message: this.deleteMessage,
                messageIsHtml: true,
            });
        }
    }
    exports.Delete = Delete;
    exports.default = Delete;
});
