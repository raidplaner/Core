 /**
 * Character editing capabilities for the character list.
 *
 * @author  Marco Daries
 * @module      Daries/RP/Acp/Ui/Character/Editor
 */

import * as Core from "WoltLabSuite/Core/Core";
import DeleteAction from "./Action/DeleteAction";
import DisableAction from "./Action/DisableAction";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import * as Language from "WoltLabSuite/Core/Language";
import UiDropdownSimple from "WoltLabSuite/Core/Ui/Dropdown/Simple";

interface RefreshCharactersData {
    characterIds: number[];
}

class AcpUiCharacterEditor {
    /**
     * Initializes the edit dropdown for each character.
     */
    constructor() {
        document.querySelectorAll(".jsCharacterRow").forEach((characterRow: HTMLTableRowElement) => this.initCharacter(characterRow));

        EventHandler.add("dev.daries.rp.acp.character", "refresh", (data: RefreshCharactersData) => this.refreshCharacters(data));
    }
    
    /**
     * Initializes the edit dropdown for a character.
     */
    private initCharacter(characterRow: HTMLTableRowElement): void {
        const characterId = ~~characterRow.dataset.objectId!;
        const dropdownId = `characterListDropdown${characterId}`;
        const dropdownMenu = UiDropdownSimple.getDropdownMenu(dropdownId)!;
        
        if (dropdownMenu.childElementCount === 0) {
            const toggleButton = characterRow.querySelector(".dropdownToggle") as HTMLAnchorElement;
            toggleButton.classList.add("disabled");

            return;
        }
        
        const editLink = dropdownMenu.querySelector(".jsEditLink") as HTMLAnchorElement;
        if (editLink !== null) {
            const toggleButton = characterRow.querySelector(".dropdownToggle") as HTMLAnchorElement;
            toggleButton.addEventListener("dblclick", (event) => {
                event.preventDefault();

                editLink.click();
            });
        }
        
        const enableCharacter = dropdownMenu.querySelector(".jsEnable");
        if (enableCharacter !== null) {
            new DisableAction(enableCharacter as HTMLAnchorElement, characterId, characterRow);
        }
        
        const deleteCharacter = dropdownMenu.querySelector(".jsDelete");
        if (deleteCharacter !== null) {
            new DeleteAction(deleteCharacter as HTMLAnchorElement, characterId, characterRow);
        }
    }
    
    private refreshCharacters(data: RefreshCharactersData): void {
        document.querySelectorAll(".jsCharacterRow").forEach((characterRow: HTMLTableRowElement) => {
            const characterId = ~~characterRow.dataset.objectId!;
            
            if (data.characterIds.includes(characterId)) {
                const characterStatusIcons = characterRow.querySelector(".characterStatusIcons") as HTMLElement;
                
                const isDisabled = !Core.stringToBool(characterRow.dataset.enabled!);
                let iconIsDisabled = characterRow.querySelector(".jsCharacterDisabled") as HTMLElement;
                if (isDisabled && iconIsDisabled === null) {
                    iconIsDisabled = document.createElement("span");
                    iconIsDisabled.className = "icon icon16 fa-power-off jsCharacterDisabled jsTooltip";
                    iconIsDisabled.title = Language.get("rp.acp.character.isDisabled");
                    characterStatusIcons.appendChild(iconIsDisabled);
                } else if (!isDisabled && iconIsDisabled !== null) {
                    iconIsDisabled.remove();
                }
            }
        });
    }
}

export = AcpUiCharacterEditor;