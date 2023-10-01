 /**
 * @author  Marco Daries
 * @module      Daries/RP/Ui/Event/Manager
 */

import * as Core from "WoltLabSuite/Core/Core"; 
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import Manager from "WoltLabSuite/Core/Ui/Message/Manager";
import { DatabaseObjectActionResponse } from "WoltLabSuite/Core/Ajax/Data";

class UiEventManager extends Manager {
    protected readonly eventId: number;
    
    constructor(eventId: number) {
        super({
            className: "rp\\data\\event\\EventAction",
            selector: ".rpEventHeader",
        });
        
        this.eventId = eventId;
    }
    
    _ajaxSuccess(_data: DatabaseObjectActionResponse): void {
        EventHandler.fire("Daries/RP/Ui/Event/Manager", "_ajaxSuccess", _data);
    }
}

Core.enableLegacyInheritance(UiEventManager);

export = UiEventManager;