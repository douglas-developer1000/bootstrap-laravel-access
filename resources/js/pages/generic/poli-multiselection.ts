/**
 * CSS classes required:
 *
 * - .multiselection-all in a [input type="checkbox"]
 * - .multiselection-items in some [input type="checkbox"]
 * - .multiselection-submit in one or more button[type="submit"]
 *
 * Each isolated group must have a unique data-key attribute defined as dom attribute
 */

const domReferences = {
    allCheckList: Array.from(
        document.querySelectorAll<HTMLInputElement>(
            "input[type='checkbox'].multiselection-all",
        ),
    ),
    checkItemList: Array.from(
        document.querySelectorAll<HTMLInputElement>(
            "input[type='checkbox'].multiselection-item:not(:disabled)",
        ),
    ),
    submiBtnList: Array.from(
        document.querySelectorAll<HTMLButtonElement>(".multiselection-submit"),
    ),
};
type DomReferences = typeof domReferences;

function updateAllCheckIsolated(
    itemList: HTMLInputElement[],
    $allCheck?: HTMLInputElement,
) {
    Object.assign($allCheck ?? {}, {
        checked:
            itemList.length > 0 && itemList.every(($item) => $item.checked),
    });
}
function splitByKey(key: string, domReferences: DomReferences) {
    const $allCheck = domReferences.allCheckList.find(
        ($allCheck) => $allCheck.dataset.key === key,
    );
    const itemList = domReferences.checkItemList.filter(
        ($item) => $item.dataset.key === key,
    );
    const $submitBtnList = domReferences.submiBtnList.filter(
        ($btn) => $btn.dataset.key === key,
    );
    return {
        list: itemList,
        allCheck: $allCheck,
        submitBtnList: $submitBtnList,
    };
}
type Splitted = ReturnType<typeof splitByKey>;

function updateAllCheckByKey(
    key: string,
    domReferences: DomReferences,
    ref: Splitted | null = null,
) {
    const { list, allCheck } = ref ?? splitByKey(key, domReferences);
    updateAllCheckIsolated(list, allCheck);
}
function updateSubmitBtnByKey(
    key: string,
    domReferences: DomReferences,
    ref: Splitted | null = null,
) {
    const { list, submitBtnList } = ref ?? splitByKey(key, domReferences);
    const submitBtnDisabled = list.every(($item) => !$item.checked);
    submitBtnList.forEach(($btn) => {
        $btn.disabled = submitBtnDisabled;
    });
}

/**
 * Beginning execution
 */
domReferences.allCheckList.forEach(($allCheck) => {
    const key = $allCheck.dataset.key ?? "";
    const ref = splitByKey(key, domReferences);

    updateAllCheckByKey(key, domReferences, ref);
    updateSubmitBtnByKey(key, domReferences, ref);

    $allCheck.addEventListener("change", () => {
        ref.list.forEach(($item) => {
            $item.checked = $allCheck.checked;
        });

        updateSubmitBtnByKey(key, domReferences, ref);
    });

    ref.list.forEach(($item) => {
        $item.addEventListener("change", () => {
            if (!$item.checked && $allCheck.checked) {
                $allCheck.checked = false;
            }
            if ($item.checked && !$allCheck.checked) {
                updateAllCheckByKey(key, domReferences, ref);
            }
            updateSubmitBtnByKey(key, domReferences, ref);
        });
    });

    ref.submitBtnList.forEach(($btn) => {
        $btn.addEventListener("click", () => {
            ref.list.forEach(($check) => {
                $check.setAttribute("form", $btn.dataset.form ?? "");
                $check.setAttribute("name", $btn.dataset.name ?? "");
            });
        });
    });
});
