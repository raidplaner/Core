define(["require", "exports", "tslib", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Dom/Util", "WoltLabSuite/Core/Event/Handler"], function (require, exports, tslib_1, Ajax, Core, Util_1, EventHandler) {
    "use strict";
    Ajax = tslib_1.__importStar(Ajax);
    Core = tslib_1.__importStar(Core);
    Util_1 = tslib_1.__importDefault(Util_1);
    EventHandler = tslib_1.__importStar(EventHandler);
    class ChracterProfileTabMenu {
        constructor(profileContent, characterID) {
            this._contents = new Map();
            this._profileContent = profileContent;
            this._characterID = characterID;
            const activeMenuItem = this._profileContent.dataset.active;
            let hasNotContent = false;
            this._profileContent.querySelectorAll("div.tabMenuContent").forEach((container) => {
                const containerID = container.dataset.name;
                if (activeMenuItem === containerID) {
                    this._contents.set(containerID, true);
                }
                else {
                    this._contents.set(containerID, false);
                    hasNotContent = true;
                }
            });
            if (hasNotContent) {
                this._profileContent.querySelectorAll("nav.tabMenu > ul > li").forEach((listItem) => {
                    if (listItem.classList.contains("ui-state-active")) {
                        this._loadContent(document.getElementById(listItem.dataset.name));
                        return false;
                    }
                });
            }
            EventHandler.add("com.woltlab.wcf.simpleTabMenu_" + this._profileContent.id, "select", (data) => this._loadContent(data.active));
        }
        _loadContent(panel) {
            if (panel.tagName === "LI") {
                panel = document.getElementById(panel.dataset.name);
            }
            const containerID = Util_1.default.identify(panel);
            const hasContent = this._contents.get(containerID);
            if (!hasContent) {
                Ajax.api(this, {
                    actionName: "getContent",
                    parameters: {
                        characterID: this._characterID,
                        containerID: containerID,
                        menuItem: panel.dataset.menuItem
                    }
                });
            }
        }
        _ajaxSetup() {
            return {
                data: {
                    className: "rp\\data\\character\\profile\\menu\\item\\CharacterProfileMenuItemAction"
                }
            };
        }
        _ajaxSuccess(data) {
            const containerID = data.returnValues.containerID;
            this._contents.set(containerID, true);
            Util_1.default.insertHtml(data.returnValues.template, document.getElementById(containerID), "append");
        }
    }
    Core.enableLegacyInheritance(ChracterProfileTabMenu);
    return ChracterProfileTabMenu;
});
