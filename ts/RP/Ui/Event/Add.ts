/**
 * Provides the dialog overlay to add a new event.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */

import * as Language from "WoltLabSuite/Core/Language";
import { DialogCallbackObject, DialogCallbackSetup } from "WoltLabSuite/Core/Ui/Dialog/Data";
import UiDialog from "WoltLabSuite/Core/Ui/Dialog";

class EventAdd implements DialogCallbackObject {
    constructor(private readonly link: string) {
        document.querySelectorAll(".jsButtonEventAdd").forEach((button: HTMLElement) => {
            button.addEventListener("click", (ev) => this.openDialog(ev));
        });
    }

    openDialog(event?: MouseEvent): void {
        if (event instanceof Event) {
            event.preventDefault();
        }

        UiDialog.open(this);
    }

    _dialogSetup(): ReturnType<DialogCallbackSetup> {
        return {
            id: "eventAddDialog",
            options: {
                onSetup: (content) => {
                    const button = content.querySelector("button") as HTMLElement;
                    button.addEventListener("click", (event) => {
                        event.preventDefault();

                        const input = content.querySelector('input[name="objectTypeID"]:checked') as HTMLInputElement;

                        window.location.href = this.link.replace("{$objectTypeID}", input.value);
                    });
                },
                title: Language.get("rp.event.add")
            },
        };
    }
}

let eventAdd: EventAdd;

/**
 * Initializes the event add handler.
 */
export function init(link: string): void {
  if (!eventAdd) {
    eventAdd = new EventAdd(link);
  }
}

/**
 * Opens the 'Add Event' dialog.
 */
export function openDialog(): void {
  eventAdd.openDialog();
}