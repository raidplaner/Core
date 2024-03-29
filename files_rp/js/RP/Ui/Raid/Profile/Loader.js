define(["require", "exports", "tslib", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Dom/Util", "WoltLabSuite/Core/Language"], function (require, exports, tslib_1, Ajax, Core, Util_1, Language) {
    "use strict";
    Ajax = tslib_1.__importStar(Ajax);
    Core = tslib_1.__importStar(Core);
    Util_1 = tslib_1.__importDefault(Util_1);
    Language = tslib_1.__importStar(Language);
    class RaidCharacterProfileLoader {
        /**
         * Initializes a new RaidCharacterProfileLoader object.
         */
        constructor(characterID) {
            this._container = document.getElementById("raidList");
            this._characterID = characterID;
            this._options = {
                parameters: {},
            };
            if (!this._characterID) {
                throw new Error("[Daries/RP/Raid/Character/Profile/Loader] Invalid parameter 'characterID' given.");
            }
            const loadButtonList = document.createElement("li");
            loadButtonList.className = "raidListMore showMore";
            this._noMoreEntries = document.createElement("small");
            this._noMoreEntries.innerHTML = Language.get("rp.character.raid.noMoreEntries");
            this._noMoreEntries.style.display = "none";
            loadButtonList.appendChild(this._noMoreEntries);
            this._loadButton = document.createElement("button");
            this._loadButton.className = "small";
            this._loadButton.innerHTML = Language.get("rp.character.raid.more");
            this._loadButton.addEventListener("click", () => this._loadRaids());
            this._loadButton.style.display = "none";
            loadButtonList.appendChild(this._loadButton);
            this._container.appendChild(loadButtonList);
            if (document.querySelectorAll("#raidList > li").length === 1) {
                this._noMoreEntries.style.display = "";
            }
            else {
                this._loadButton.style.display = "";
            }
        }
        /**
         * Load a list of raids.
         */
        _loadRaids() {
            this._options.parameters.characterID = this._characterID;
            this._options.parameters.lastRaidTime = ~~this._container.dataset.lastRaidTime;
            Ajax.api(this, {
                parameters: this._options.parameters
            });
        }
        _ajaxSetup() {
            return {
                data: {
                    actionName: "load",
                    className: "\\rp\\data\\raid\\RaidAction",
                },
            };
        }
        _ajaxSuccess(data) {
            if (data.returnValues.template) {
                document
                    .querySelector("#raidList > li:last-child")
                    .insertAdjacentHTML("beforebegin", data.returnValues.template);
                this._container.dataset.lastRaidTime = data.returnValues.lastRaidTime.toString();
                Util_1.default.hide(this._noMoreEntries);
                Util_1.default.show(this._loadButton);
            }
            else {
                Util_1.default.show(this._noMoreEntries);
                Util_1.default.hide(this._loadButton);
            }
        }
    }
    Core.enableLegacyInheritance(RaidCharacterProfileLoader);
    return RaidCharacterProfileLoader;
});
