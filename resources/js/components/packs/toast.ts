function loadToast() {
    if (window.toastShow) {
        const instance = document.querySelector("#liveToast");
        window.bootstrap.Toast.getOrCreateInstance(instance)?.show();
    }
}
setTimeout(() => loadToast(), 0);
