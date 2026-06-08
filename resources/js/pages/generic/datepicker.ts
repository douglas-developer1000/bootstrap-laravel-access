import flatpickr from "flatpickr";
import { Portuguese } from "flatpickr/dist/l10n/pt.js";
// @ts-ignore
import "flatpickr/dist/flatpickr.min.css";

document
    .querySelectorAll<HTMLInputElement>("[data-dtpicker]")
    .forEach(($input) => {
        flatpickr($input, {
            locale: Portuguese,
        });
    });
