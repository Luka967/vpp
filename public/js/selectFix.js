(() => {
    window.addEventListener('load', () => {
        document.querySelectorAll('select').forEach(inputElem => {
            const value = inputElem.getAttribute('data-value');
            if (value == null)
                return;
            inputElem.value = value;
            inputElem.removeAttribute('data-value');
        });
    });
})();
