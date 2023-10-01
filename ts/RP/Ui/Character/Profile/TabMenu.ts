/**
 * Reads character specific content of the selected tab.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */

import * as Ajax from "WoltLabSuite/Core/Ajax";
import { AjaxCallbackSetup, ResponseData } from "WoltLabSuite/Core/Ajax/Data";
import * as Core from "WoltLabSuite/Core/Core";
import DomUtil from "WoltLabSuite/Core/Dom/Util";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";

class ChracterProfileTabMenu {
    protected readonly _characterID: number;
    protected readonly _contents = new Map<string, boolean>();
    protected readonly _profileContent: HTMLElement;

    constructor(profileContent: HTMLElement, characterID: number) {
        this._profileContent = profileContent;
        this._characterID = characterID;

        const activeMenuItem = this._profileContent.dataset.active;
        let hasNotContent = false;
        this._profileContent.querySelectorAll("div.tabMenuContent").forEach((container: HTMLElement) => {
            const containerID = container.dataset.name!;

            if (activeMenuItem === containerID) {
                this._contents.set(containerID, true);
            } else {
                this._contents.set(containerID, false);
                hasNotContent = true;
            }
        });

        if (hasNotContent) {
            this._profileContent.querySelectorAll("nav.tabMenu > ul > li").forEach((listItem: HTMLElement) => {
                if (listItem.classList.contains("ui-state-active")) {
                    this._loadContent(document.getElementById(listItem.dataset.name!) as HTMLElement);
                    
                    return false;
                }
            });
        }
        
        EventHandler.add("com.woltlab.wcf.simpleTabMenu_" + this._profileContent.id, "select", (data) => this._loadContent(data.active));
    }
    
    protected _loadContent(panel: HTMLElement): void {
        if (panel.tagName === "LI") {
            panel = document.getElementById(panel.dataset.name!) as HTMLElement;
        }
        
        const containerID = DomUtil.identify(panel);
        
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

    _ajaxSetup(): ReturnType<AjaxCallbackSetup> {
        return {
            data: {
                className: "rp\\data\\character\\profile\\menu\\item\\CharacterProfileMenuItemAction"
            }
        };
    }
    
    _ajaxSuccess(data: AjaxResponse): void {
        const containerID = data.returnValues.containerID;
        this._contents.set(containerID, true);
        
        DomUtil.insertHtml(data.returnValues.template, document.getElementById(containerID)!, "append");
    }
}

Core.enableLegacyInheritance(ChracterProfileTabMenu);

export = ChracterProfileTabMenu;

interface AjaxResponse extends ResponseData {
    returnValues: {
        template: string;
        containerID: string;
    };
}