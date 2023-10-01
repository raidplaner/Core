 /**
 * An abstract action, to handle character actions.
 *
 * @author  Marco Daries
 * @module      Daries/RP/Acp/Ui/Character/Action/Abstract
 */

export abstract class AbstractCharacterAction {
    protected readonly button: HTMLElement;
    protected readonly characterDataElement: HTMLElement;
    protected readonly characterId: number;

    public constructor(button: HTMLElement, characterId: number, characterDataElement: HTMLElement) {
        this.button = button;
        this.characterId = characterId;
        this.characterDataElement = characterDataElement;
  }
}

export default AbstractCharacterAction;