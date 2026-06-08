const updateView = ($select: HTMLSelectElement, $forms: HTMLFormElement[]) => {
    $forms.forEach(($form) => {
        if ($form.dataset.show === $select.value) {
            $form.classList.add("show");
        } else {
            $form.classList.remove("show");
        }
    });
};
const switchType = () => {
    const $forms = Array.from(
        document.querySelectorAll<HTMLFormElement>("form[data-show]"),
    );
    const $select = document.querySelector<HTMLSelectElement>("#type");
    if ($select === null) {
        return;
    }
    $select.addEventListener("change", () => {
        updateView($select, $forms);
    });
    updateView($select, $forms);
};

switchType();
