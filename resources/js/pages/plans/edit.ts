const $additionalStates = Array.from(
    document.querySelectorAll<HTMLInputElement>('input[name="additionals[]"]'),
);
const $assignedRoles = Array.from(
    document.querySelectorAll<HTMLInputElement>('input[name="roles[]"]'),
);

$assignedRoles.forEach(($input, i) => {
    $input.addEventListener("change", () => {
        const $add = $additionalStates[i];
        if (typeof $add === "undefined") {
            return;
        }
        $add.disabled = !$input.checked;
    });
});
