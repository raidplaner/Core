 /**
 * Handles a character disable/enable button.
 *
 * @author  Marco Daries
 * @module      Daries/RP/Acp/Ui/Character/Action/DeleteAction
 */

import AbstractCharacterAction from "./Abstract";
import * as Ajax from "WoltLabSuite/Core/Ajax";
import { AjaxCallbackObject, AjaxCallbackSetup, DatabaseObjectActionResponse } from "WoltLabSuite/Core/Ajax/Data";
import * as Core from "WoltLabSuite/Core/Core";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

export class DisableAction extends AbstractCharacterAction implements AjaxCallbackObject {
    public constructor(button: HTMLElement, characterId: number, characterDataElement: HTMLElement) {
        super(button, characterId, characterDataElement);
        
        this.button.addEventListener("click", (event) => {
            event.preventDefault();
            const isEnabled = Core.stringToBool(this.characterDataElement.dataset.enabled!);

            Ajax.api(this, {
                actionName: isEnabled ? "disable" : "enable",
            });
        });
    }
    
    _ajaxSetup(): ReturnType<AjaxCallbackSetup> {
        return {
            data: {
                className: "rp\\data\\character\\CharacterAction",
                objectIDs: [this.characterId],
            },
        };
    }
    
    _ajaxSuccess(data: DatabaseObjectActionResponse): void {
        data.objectIDs.forEach((objectId) => {
            if (~~objectId == this.characterId) {
                switch (data.actionName) {
                    case "enable":
                        this.characterDataElement.dataset.enabled = "true";
                        this.button.textContent = this.button.dataset.disableMessage!;
                        break;

                    case "disable":
                        this.characterDataElement.dataset.enabled = "false";
                        this.button.textContent = this.button.dataset.enableMessage!;
                        break;

                    default:
                        throw new Error("Unreachable");
                }
            }
        });

        UiNotification.show();

        EventHandler.fire("dev.daries.rp.acp.character", "refresh", {
            characterIds: [this.characterId],
        });
  }
}

export default DisableAction;