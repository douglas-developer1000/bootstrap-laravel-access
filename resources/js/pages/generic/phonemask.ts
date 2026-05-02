import InputmaskRef from "inputmask";

declare global {
    interface Window {
        Inputmask: typeof InputmaskRef;
    }
}

new Inputmask("(99) 99999-9999").mask("#phone");
