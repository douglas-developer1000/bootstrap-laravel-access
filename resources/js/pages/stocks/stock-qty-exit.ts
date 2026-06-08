interface UsesDataItem {
    input: HTMLInputElement;
    addBtn: HTMLButtonElement;
    subBtn: HTMLButtonElement;
}

const addBtnUpdating = (data: UsesDataItem) => {
    if (data.input.valueAsNumber === Number(data.input.max)) {
        data.addBtn.disabled = true;
    }
    if (data.subBtn.disabled) {
        data.subBtn.disabled = false;
    }
};

const buildUsesData = (
    $parent: HTMLDivElement,
    list: UsesDataItem[] = [],
): UsesDataItem[] => {
    return Array.from($parent.querySelectorAll<HTMLDivElement>(".uses")).reduce(
        (acc, $div, i) => {
            const $input = $div.querySelector<HTMLInputElement>("input");
            const $addBtn = $div.querySelector<HTMLButtonElement>(".add");
            const $subBtn = $div.querySelector<HTMLButtonElement>(".sub");
            if (!$input || !$addBtn || !$subBtn) {
                return acc;
            }
            acc[i] = {
                input: $input,
                addBtn: $addBtn,
                subBtn: $subBtn,
            };
            return acc;
        },
        list,
    );
};

const runTotalSum = (
    $totalValue: HTMLDivElement | null,
    useInputs: HTMLInputElement[],
) => {
    ($totalValue ?? { innerHTML: "" }).innerHTML = useInputs
        .reduce((acc, next) => acc + Number(next.value), 0)
        .toString()
        .padStart(3, "0");
};

const subBtnUpdating = (data: UsesDataItem) => {
    if (data.input.valueAsNumber === 0) {
        data.subBtn.disabled = true;
    }
    if (data.addBtn.disabled) {
        data.addBtn.disabled = false;
    }
};

const handleActions = () => {
    const parents = Array.from<HTMLDivElement>(
        document.querySelectorAll(".table-container"),
    );

    parents.forEach(($parent) => {
        const $totalValue =
            $parent.querySelector<HTMLDivElement>(".total .value");
        const useInputs: HTMLInputElement[] = [];
        const usesData = buildUsesData($parent);
        usesData.forEach((data) => {
            useInputs.push(data.input);
            addBtnUpdating(data);
            data.addBtn.addEventListener("click", () => {
                if (data.input.valueAsNumber + 1 <= Number(data.input.max)) {
                    data.input.stepUp();
                    runTotalSum($totalValue, useInputs);

                    addBtnUpdating(data);
                }
            });
            subBtnUpdating(data);
            data.subBtn.addEventListener("click", () => {
                if (data.input.valueAsNumber - 1 >= 0) {
                    data.input.stepDown();
                    runTotalSum($totalValue, useInputs);

                    subBtnUpdating(data);
                }
            });
        });
        runTotalSum($totalValue, useInputs);
    });
};
handleActions();
