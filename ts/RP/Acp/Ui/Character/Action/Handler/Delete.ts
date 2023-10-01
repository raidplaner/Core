/**
 * Deletes a given character.
 *
 * @author  Marco Daries
 * @module      Daries/RP/Acp/Ui/Character/Action/Handler/Delete
 */

import * as Ajax from "WoltLabSuite/Core/Ajax";
import { CallbackSuccess } from "WoltLabSuite/Core/Ajax/Data";
import * as UiConfirmation from "WoltLabSuite/Core/Ui/Confirmation";

export class Delete {
    private characterIDs: number[];
    private successCallback: CallbackSuccess;
    private deleteMessage: string;

    public constructor(characterIDs: number[], successCallback: CallbackSuccess, deleteMessage: string) {
        this.characterIDs = characterIDs;
        this.successCallback = successCallback;
        this.deleteMessage = deleteMessage;
    }

    public delete(): void {
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

export default Delete;