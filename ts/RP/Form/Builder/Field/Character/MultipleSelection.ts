/**
 * Handle other characters these Users by selection.
 *
 * @author  Marco Daries
 * @module      Daries/RP/Form/Builder/Field/Character/MultipleSelection
 */

import * as Core from "WoltLabSuite/Core/Core";

class CharacterMultipleSelection {
    protected readonly _element: HTMLElement;
    
    constructor(elementId: string) {
        this._element = document.getElementById(elementId)as HTMLElement;
        this._element.querySelectorAll("input").forEach((input) => {
           input.addEventListener("change", (ev) => this._change(ev)); 
        });
    }
    
    protected _change(event: Event): void {
        const element = event.currentTarget as HTMLInputElement;
        const userId = ~~element.dataset.userId!;
        const value = element.value;
        const checked = element.checked;
        
        this._element.querySelectorAll("input").forEach((input) => {
            if (userId === ~~input.dataset.userId! &&
                value !== input.value) {
                if (checked) input.disabled = true;
                else input.disabled = false; 
            }
        });
    }
}

Core.enableLegacyInheritance(CharacterMultipleSelection);

export = CharacterMultipleSelection;