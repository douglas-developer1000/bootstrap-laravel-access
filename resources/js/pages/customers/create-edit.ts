import flatpickr from "flatpickr";
import { Portuguese } from "flatpickr/dist/l10n/pt.js";
// @ts-ignore
import "flatpickr/dist/flatpickr.min.css";

flatpickr("#birthdate-field", {
    locale: Portuguese,
});
