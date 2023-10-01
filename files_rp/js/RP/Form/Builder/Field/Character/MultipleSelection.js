define(["require", "exports", "tslib", "WoltLabSuite/Core/Core"], function (require, exports, tslib_1, Core) {
    "use strict";
    Core = tslib_1.__importStar(Core);
    class CharacterMultipleSelection {
        constructor(elementId) {
            this._element = document.getElementById(elementId);
            this._element.querySelectorAll("input").forEach((input) => {
                input.addEventListener("change", (ev) => this._change(ev));
            });
        }
        _change(event) {
            const element = event.currentTarget;
            const userId = ~~element.dataset.userId;
            const value = element.value;
            const checked = element.checked;
            this._element.querySelectorAll("input").forEach((input) => {
                if (userId === ~~input.dataset.userId &&
                    value !== input.value) {
                    if (checked)
                        input.disabled = true;
                    else
                        input.disabled = false;
                }
            });
        }
    }
    Core.enableLegacyInheritance(CharacterMultipleSelection);
    return CharacterMultipleSelection;
});
