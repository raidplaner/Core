define(["require", "exports", "tslib", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Ui/Dialog"], function (require, exports, tslib_1, Language, Dialog_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.openDialog = exports.init = void 0;
    Language = tslib_1.__importStar(Language);
    Dialog_1 = tslib_1.__importDefault(Dialog_1);
    class EventAdd {
        constructor(link) {
            this.link = link;
            document.querySelectorAll(".jsButtonEventAdd").forEach((button) => {
                button.addEventListener("click", (ev) => this.openDialog(ev));
            });
        }
        openDialog(event) {
            if (event instanceof Event) {
                event.preventDefault();
            }
            Dialog_1.default.open(this);
        }
        _dialogSetup() {
            return {
                id: "eventAddDialog",
                options: {
                    onSetup: (content) => {
                        const button = content.querySelector("button");
                        button.addEventListener("click", (event) => {
                            event.preventDefault();
                            const input = content.querySelector('input[name="objectTypeID"]:checked');
                            window.location.href = this.link.replace("{$objectTypeID}", input.value);
                        });
                    },
                    title: Language.get("rp.event.add")
                },
            };
        }
    }
    let eventAdd;
    /**
     * Initializes the event add handler.
     */
    function init(link) {
        if (!eventAdd) {
            eventAdd = new EventAdd(link);
        }
    }
    exports.init = init;
    /**
     * Opens the 'Add Event' dialog.
     */
    function openDialog() {
        eventAdd.openDialog();
    }
    exports.openDialog = openDialog;
});
