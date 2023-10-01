 /**
 * Handles a character delete button.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
 
import AbstractCharacterAction from "./Abstract";
import Delete from "./Handler/Delete";

export class DeleteAction extends AbstractCharacterAction {
    public constructor(button: HTMLElement, characterId: number, characterDataElement: HTMLElement) {
        super(button, characterId, characterDataElement);
        
        if (typeof this.button.dataset.confirmMessage !== "string") {
            throw new Error("The button does not provide a confirmMessage.");
        }
        
        this.button.addEventListener("click", (event) => {
            event.preventDefault();

            const deleteHandler = new Delete(
                [this.characterId],
                () => {
                    this.characterDataElement.remove();
                },
                this.button.dataset.confirmMessage!,
            );
            deleteHandler.delete();
        });
    }
}

export default DeleteAction;