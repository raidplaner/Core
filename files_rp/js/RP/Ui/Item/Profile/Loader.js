define(["require", "exports", "tslib", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Dom/Util", "WoltLabSuite/Core/Language"], function (require, exports, tslib_1, Ajax, Core, Util_1, Language) {
    "use strict";
    Ajax = tslib_1.__importStar(Ajax);
    Core = tslib_1.__importStar(Core);
    Util_1 = tslib_1.__importDefault(Util_1);
    Language = tslib_1.__importStar(Language);
    class ItemCharacterProfileLoader {
        /**
         * Initializes a new ItemCharacterProfileLoader object.
         */
        constructor(characterID) {
            this._container = document.getElementById("itemList");
            this._characterID = characterID;
            this._options = {
                parameters: {},
            };
            if (!this._characterID) {
                throw new Error("[Daries/RP/Item/Character/Profile/Loader] Invalid parameter 'characterID' given.");
            }
            const loadButtonList = document.createElement("li");
            loadButtonList.className = "itemListMore showMore";
            this._noMoreEntries = document.createElement("small");
            this._noMoreEntries.innerHTML = Language.get("rp.character.item.noMoreEntries");
            this._noMoreEntries.style.display = "none";
            loadButtonList.appendChild(this._noMoreEntries);
            this._loadButton = document.createElement("button");
            this._loadButton.className = "small";
            this._loadButton.innerHTML = Language.get("rp.character.item.more");
            this._loadButton.addEventListener("click", () => this._loadItems());
            this._loadButton.style.display = "none";
            loadButtonList.appendChild(this._loadButton);
            this._container.after(loadButtonList);
            if (document.querySelectorAll("#itemList > li").length === 1) {
                this._noMoreEntries.style.display = "";
            }
            else {
                this._loadButton.style.display = "";
            }
        }
        /**
         * Load a list of items.
         */
        _loadItems() {
            this._options.parameters.characterID = this._characterID;
            this._options.parameters.lastItemOffset = ~~this._container.dataset.lastItemOffset;
            Ajax.api(this, {
                parameters: this._options.parameters
            });
        }
        _ajaxSetup() {
            return {
                data: {
                    actionName: "load",
                    className: "\\rp\\data\\item\\ItemAction",
                },
            };
        }
        _ajaxSuccess(data) {
            if (data.returnValues.template) {
                document
                    .querySelector("#itemList > li:last-child")
                    .insertAdjacentHTML("beforebegin", data.returnValues.template);
                this._container.dataset.lastItemOffset = data.returnValues.lastItemOffset.toString();
                Util_1.default.hide(this._noMoreEntries);
                Util_1.default.show(this._loadButton);
            }
            else {
                Util_1.default.show(this._noMoreEntries);
                Util_1.default.hide(this._loadButton);
            }
        }
    }
    Core.enableLegacyInheritance(ItemCharacterProfileLoader);
    return ItemCharacterProfileLoader;
});
