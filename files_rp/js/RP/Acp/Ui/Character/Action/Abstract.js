define(["require", "exports"], function (require, exports) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.AbstractCharacterAction = void 0;
    /**
    * An abstract action, to handle character actions.
    *
 * @author  Marco Daries
    * @module      Daries/RP/Acp/Ui/Character/Action/Abstract
    */
    class AbstractCharacterAction {
        constructor(button, characterId, characterDataElement) {
            this.button = button;
            this.characterId = characterId;
            this.characterDataElement = characterDataElement;
        }
    }
    exports.AbstractCharacterAction = AbstractCharacterAction;
    exports.default = AbstractCharacterAction;
});
