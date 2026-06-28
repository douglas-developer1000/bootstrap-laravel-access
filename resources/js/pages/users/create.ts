const $aditionals = Array.from(
    document.querySelectorAll<HTMLUListElement>("[data-slug]"),
);
const $addPlaceholder = document.querySelector<HTMLDivElement>(
    ".additional-placeholder",
);

const $planCombobox = document.querySelector<HTMLSelectElement>(
    'select[name="plan"]',
);

const updateAdditionals = (
    $unList: HTMLUListElement[],
    $placeholder: HTMLDivElement | null,
    slugValue = "",
) => {
    $unList.forEach(($ul) => {
        if (slugValue !== "" && !$placeholder?.classList.contains("hide")) {
            $placeholder?.classList.add("hide");
        }
        if ($ul.dataset.slug === slugValue) {
            $ul.classList.add("show");
            $ul.querySelectorAll<HTMLInputElement>(
                'input[type="checkbox"]',
            ).forEach(($input) => {
                $input.disabled = false;
            });
        } else {
            $ul.classList.remove("show");
            $ul.querySelectorAll<HTMLInputElement>(
                'input[type="checkbox"]',
            ).forEach(($input) => {
                $input.disabled = true;
            });
        }
    });
};

$planCombobox?.addEventListener("change", () => {
    const slugValue = $planCombobox.value;
    updateAdditionals($aditionals, $addPlaceholder, slugValue);
});

updateAdditionals($aditionals, $addPlaceholder, $planCombobox?.value);
