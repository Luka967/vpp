(() => {
    window.addEventListener('load', () => {
        let redirecting = false;

        const elems = loadIds('login-button', 'login-form', 'register-button', 'register-form');
        if (elems['login-button'] != null) {
            elems['login-button'].addEventListener('click', ev => {
                location.href = '/login';
                redirecting = true;
            });
            elems['register-form'].addEventListener('submit', ev => {
                if (!redirecting)
                    return true;
                ev.preventDefault();
                return false;
            });
        }
        if (elems['register-button'] != null) {
            elems['register-button'].addEventListener('click', ev => {
                location.href = '/register';
                redirecting = true;
            });
            elems['login-form'].addEventListener('submit', ev => {
                if (!redirecting)
                    return true;
                ev.preventDefault();
                return false;
            });
        }
    });
})();
