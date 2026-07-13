const $div = document.querySelector<HTMLDivElement>('.final-price');
const dataset = {
    planPrice: Number($div?.dataset.planPrice ?? 0),
    additionalBasePrice: Number($div?.dataset.additionalPrice ?? 0),
    discount: Number($div?.dataset.discount ?? 0)
};
const checkboxes = Array.from(document.querySelectorAll<HTMLInputElement>('input[name="additionals[]"]'));

function updatePrice(checkboxes: HTMLInputElement[], data: typeof dataset, $priceBox: HTMLDivElement | null) {
    if (!$priceBox) {
        return;
    }
    const price = checkboxes.reduce(
        (acc, next) => acc + (Number(next.checked) * data.additionalBasePrice),
        data.planPrice - data.discount
    );
    if (price > 0) {
        $priceBox.innerHTML = `
            <span>${
                price.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                })
            }</span>
        `;
    } else {
        $priceBox.innerHTML = `
            <span>NADA!</span>
            <span>INICIO IMEDIATO!</span>
        `;
    }
}
checkboxes.forEach($input => {
    $input.addEventListener('change', () => updatePrice(checkboxes, dataset, $div));
});
updatePrice(checkboxes, dataset, $div);