define(["require", "exports", "tslib", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Ui/Search/Input"], function (require, exports, tslib_1, Core, Input_1) {
    "use strict";
    Core = tslib_1.__importStar(Core);
    Input_1 = tslib_1.__importDefault(Input_1);
    class UiCharacterSearchInput extends Input_1.default {
        constructor(element, options) {
            options = Core.extend({
                ajax: {
                    className: "rp\\data\\character\\CharacterAction",
                },
            }, options);
            super(element, options);
        }
        createListItem(item) {
            const listItem = super.createListItem(item);
            const box = document.createElement("div");
            box.className = "box16";
            box.innerHTML = item.icon;
            box.appendChild(listItem.children[0]);
            listItem.appendChild(box);
            return listItem;
        }
    }
    Core.enableLegacyInheritance(UiCharacterSearchInput);
    return UiCharacterSearchInput;
});
