const paymentCardHandle = (
    value: string,
    selectBoxes: HTMLDivElement[],
    selects: HTMLSelectElement[],
    required = false,
) => {
    if (value === "card") {
        selectBoxes.forEach(($box) => {
            $box.classList.remove("position-absolute");
            $box.classList.add("show");
            $box.classList.add("position-relative");
        });
        selects.forEach(($select) => {
            $select.disabled = false;
            if (required) {
                $select.required = true;
            }
        });
    } else {
        selectBoxes.forEach(($box) => {
            $box.classList.remove("position-relative");
            $box.classList.remove("show");
            $box.classList.add("position-absolute");
        });
        selects.forEach(($select) => {
            $select.disabled = true;
            $select.value = "";
            if (required) {
                $select.required = false;
            }
        });
    }
};

const paywayHandle = (
    value: string,
    $payWayContainer: HTMLDivElement,
    $selectCard: HTMLSelectElement,
) => {
    if (!value) {
        $payWayContainer.innerHTML = "";
        return;
    }
    const $option = $selectCard.querySelector<HTMLOptionElement>(
        `option[value="${value}"]`,
    )!;
    const payWays = ($option.dataset.payWays ?? "")?.split("+")!;
    $payWayContainer.innerHTML = payWays.reduce((acc, next) => {
        let label = "Crédito";
        if (next === "debit") {
            label = "Débito";
        }
        return (
            acc +
            `
            <input
                class='cursor-pointer form-check-input'
                type='radio'
                name='card_pay_way'
                value='${next}'
                id='${next}'
                required='required'
                ${payWays.length === 1 ? 'checked="checked"' : ""}
            />
            <label class='cursor-pointer form-check-label' for="${next}">${label}</label>
        `
        );
    }, "");
};
const cardsHandle = () => {
    const $selecPaymentType = document.querySelector<HTMLSelectElement>(
        '[name="payment-type"]',
    );
    const $selectCard = document.querySelector<HTMLSelectElement>(
        ".card-comboboxes.cards select",
    );
    const selectCardBoxes = Array.from(
        document.querySelectorAll<HTMLDivElement>(".card-comboboxes"),
    );
    const selectsNonRequired = Array.from(
        document.querySelectorAll<HTMLSelectElement>(
            ".card-comboboxes:not(.cards) select",
        ),
    );
    const $payWayContainer =
        document.querySelector<HTMLDivElement>(".pay-ways");

    if (!$selecPaymentType || !$selectCard || !$payWayContainer) {
        return;
    }
    $selecPaymentType.addEventListener("change", (evt) => {
        const value = $selecPaymentType.value;
        paymentCardHandle(value, selectCardBoxes, [$selectCard]);
        paymentCardHandle(value, selectCardBoxes, selectsNonRequired);
    });
    paymentCardHandle($selecPaymentType.value, selectCardBoxes, [$selectCard]);
    paymentCardHandle(
        $selecPaymentType.value,
        selectCardBoxes,
        selectsNonRequired,
    );

    $selectCard.addEventListener("change", (evt) => {
        const value = $selectCard.value;
        paywayHandle(value, $payWayContainer, $selectCard);
    });
    paywayHandle($selectCard.value, $payWayContainer, $selectCard);
};

cardsHandle();
