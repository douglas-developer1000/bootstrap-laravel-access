import bootstrap from "bootstrap";

declare global {
    interface Window {
        bootstrap: bootstrap;

        toastShow: boolean;
    }
}
