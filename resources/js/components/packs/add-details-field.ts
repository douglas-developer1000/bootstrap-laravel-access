const $addBtn = document.querySelector<HTMLButtonElement>(".add-row");
const [$keyInput, $valueInput] = Array.from<HTMLInputElement>(
    document.querySelectorAll(".input-section input"),
);
const $tbody = document.querySelector<HTMLElement>(".details .table tbody");
const $inputHidden = document.querySelector<HTMLInputElement>(
    '.details input[name="details"]',
);
const values: { key: string; value: string }[] = JSON.parse(
    $inputHidden?.value ?? "[]",
);

function removeHandler(index: number) {
    values.splice(index, 1);
    render($tbody, $inputHidden);
}

function makeKeydownHandler(action: () => void) {
    return (evt: KeyboardEvent) => {
        if (evt.key === "Enter") {
            evt.preventDefault();
            action();
        }
    };
}

function makeRemoveBtn(index: number) {
    const $btnRemove = document.createElement("button");
    $btnRemove.className = "btn btn-secondary btn-sm";
    $btnRemove.innerHTML = `<i class='bi bi-x-lg'></i>`;
    $btnRemove.addEventListener("pointerup", () => removeHandler(index));
    $btnRemove.addEventListener(
        "keydown",
        makeKeydownHandler(() => removeHandler(index)),
    );
    return $btnRemove;
}

function makeDiv(value: string) {
    const $div = document.createElement("div");
    $div.className = "text-truncate";
    $div.innerText = value;
    return $div;
}

function makeRow(key: string, value: string, index: number) {
    const $tr = document.createElement("tr");
    const $tdKey = document.createElement("td");
    const $tdValue = document.createElement("td");
    const $tdRemove = document.createElement("td");
    const $btnRemove = makeRemoveBtn(index);
    $tdRemove.append($btnRemove);
    $tdKey.append(makeDiv(key));
    $tdValue.append(makeDiv(value));
    $tr.append($tdKey, $tdValue, $tdRemove);
    return $tr;
}

function makeEmptyRow() {
    const $tr = document.createElement("tr");
    $tr.className = "no-values";
    $tr.innerHTML = `
        <td colspan='3'>Sem detalhes</td>
    `;
    return $tr;
}

function makeRowItems() {
    const frag = document.createDocumentFragment();
    if (values.length > 0) {
        return values.reduce((acc, next, i) => {
            acc.append(makeRow(next.key, next.value, i));
            return acc;
        }, frag);
    }
    frag.append(makeEmptyRow());
    return frag;
}

function render(
    $tbody: HTMLElement | null,
    $inputHidden: HTMLInputElement | null,
) {
    if (!$tbody || !$inputHidden) {
        return;
    }
    $inputHidden.value = JSON.stringify(values);

    const docFr = makeRowItems();
    $tbody.innerHTML = "";
    $tbody.append(docFr);
}

document
    .querySelectorAll<HTMLButtonElement>(".details-rm-btn")
    .forEach(($btn, i) => {
        $btn.addEventListener("click", () => removeHandler(i));
    });

document
    .querySelectorAll<HTMLInputElement>(".input-section input")
    .forEach(($input) =>
        $input.addEventListener(
            "keydown",
            makeKeydownHandler(() => {
                $addBtn?.click();
            }),
        ),
    );

(() => {
    if (!$addBtn || !$tbody || !$keyInput || !$valueInput || !$inputHidden) {
        return;
    }
    $addBtn.addEventListener("click", (evt) => {
        const key = $keyInput.value.trim();
        const value = $valueInput.value.trim();
        if (!key || !value) {
            return;
        }
        const item = { key, value };
        values.push(item);
        $keyInput.value = "";
        $valueInput.value = "";

        render($tbody, $inputHidden);
    });
})();
