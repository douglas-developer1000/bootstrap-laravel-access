/**
 * CSS classes required:
 *
 * - .multiselection-all in a [input type="checkbox"]
 * - .multiselection-items in some [input type="checkbox"]
 * - .multiselection-submit in one or more button[type="submit"]
 */

const $allCheck = document.querySelector<HTMLInputElement>(
    "input[type='checkbox'].multiselection-all",
)!;
const $checkItems = document.querySelectorAll<HTMLInputElement>(
    "input[type='checkbox'].multiselection-item:not(:disabled)",
);
const $submitBtns = document.querySelectorAll<HTMLButtonElement>(
    ".multiselection-submit",
);

function updateAllCheck() {
    const list = [...$checkItems];
    $allCheck.checked = list.length > 0 && list.every(($item) => $item.checked);
}

function updateSubmitBtn(...checkInputs: HTMLInputElement[]) {
    const submitBtnDisabled = checkInputs.every(($item) => !$item.checked);
    $submitBtns.forEach(($btn) => {
        $btn.disabled = submitBtnDisabled;
    });
}
updateAllCheck();
updateSubmitBtn(...$checkItems);

$allCheck.addEventListener("change", (evt) => {
    const $check = evt.target as HTMLInputElement;

    $checkItems.forEach(($item) => {
        $item.checked = $check.checked;
    });

    updateSubmitBtn(...$checkItems);
});
$checkItems.forEach(($item) => {
    $item.addEventListener("change", (evt) => {
        if (!$item.checked && $allCheck.checked) {
            $allCheck.checked = false;
        }
        if ($item.checked && !$allCheck.checked) {
            updateAllCheck();
        }
        updateSubmitBtn(...$checkItems);
    });
});
$submitBtns.forEach(($btn) => {
    $btn.addEventListener("click", () => {
        $checkItems.forEach(($check) => {
            $check.setAttribute("form", $btn.dataset.form ?? "");
            $check.setAttribute("name", $btn.dataset.name ?? "");
        });
    });
});
