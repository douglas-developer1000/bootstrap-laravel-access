const $input = document.querySelector<HTMLInputElement>('#description-input');
const $btnIn = document.querySelector<HTMLButtonElement>('#btn-in');
const $list = document.querySelector<HTMLUListElement>('#description-list');

const globalData: { list: string[] } = {
    list: []
};

function makeListItem(value: string, ...stylesClasses: string[]) {
    const $listItem = document.createElement('li');
    $listItem.className = ['list-group-item', 'position-relative', ...stylesClasses].join(' ');

    const $span = document.createElement('span');
    $span.textContent = value;

    $listItem.innerHTML = `
        ${$span.outerHTML}
        <input type='hidden' name='descriptions[]' value='${$span.innerHTML}' />
        <button
            type='button'
            class='btn position-absolute top-50 end-0 translate-middle-y remove-btn'
        >
            <i class='bi bi-x-circle-fill text-danger'></i>
        </button>
    `;
    $listItem.dataset.description = $span.innerHTML;
    return $listItem;
}

function storeInitialDescriptions(data: typeof globalData, $list: HTMLUListElement | null, check = false) {
    if (!$list || $list.classList.contains('empty')) {
        return;
    }
    globalData.list = Array.from(
        $list.querySelectorAll<HTMLLIElement>('li.list-group-item')
    ).map(($li) => $li.dataset.description ?? '');
}

function loadDescriptions(data: typeof globalData, $list: HTMLUListElement | null, check = false) {
    if (!$list || (!check && $list.classList.contains('empty'))) {
        return;
    }
    if (check) {
        $list.classList.remove('empty');
    }
    $list.innerHTML = $list.classList.contains('empty') || data.list.length === 0
        ? `<li class='list-group-item text-danger bg-secondary-subtle'>Sem Descrições</li>`
        : data.list.map(content => {
            return makeListItem(content).outerHTML;
        }).join('')
}

$input?.addEventListener('keydown', evt => {
    if (evt.key === 'Enter') {
        $btnIn?.click();
        evt.preventDefault();
    }
});

$btnIn?.addEventListener('click', () => {
    if (!$input) {
        return;
    }
    const value = $input.value.trim();
    if (!value) {
        return;
    }
    globalData.list.push(value);
    loadDescriptions(globalData, $list, true);
    $input.value = '';
});

$list?.addEventListener('click', evt => {
    const $target = evt.target as HTMLElement | null;
    if (!$target) {
        return;
    }
    const $btn = $target.closest('.remove-btn');
    if (!$btn || !$list?.contains($btn)) {
        return;
    }
    const $listItem = $btn.closest('.list-group-item') as HTMLElement;

    const description = $listItem.dataset.description ?? '';
    globalData.list = globalData.list.filter(text => text !== description);
    loadDescriptions(globalData, $list);
});

storeInitialDescriptions(globalData, $list);
loadDescriptions(globalData, $list);
