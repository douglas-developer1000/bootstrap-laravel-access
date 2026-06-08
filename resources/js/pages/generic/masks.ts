import InputmaskRef from "inputmask";

declare global {
    interface Window {
        Inputmask: typeof InputmaskRef;
    }
}

new Inputmask("(99) 99999-9999").mask("[data-mask='phone']");
new Inputmask("**.***.***/****-**").mask("[data-mask='cnpj']");
new Inputmask("decimal", {
    min: 0,
    digits: 2,
    allowMinus: false,
    clearIncomplete: true,
    numericInput: true,
}).mask("[data-mask='float-positive']");
