const docFrag = Array.from(
    document.querySelectorAll('[data-realocate="confirm-modal"]'),
).reduce((acc, $next) => {
    acc.append($next);
    return acc;
}, document.createDocumentFragment());

document.body.append(docFrag);
