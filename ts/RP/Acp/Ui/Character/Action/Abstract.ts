 /**
 * An abstract action, to handle character actions.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
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