(() => {
    window.addEventListener('load', () => {
        const elems = loadIds(
            'logo', 'mobile-hamburger', 'mobile-nav',
            'hamburger-img'
        );
        let clickedCount = 0;

        elems.logo.addEventListener('click', () => {
            location.href = '/';
        });
        elems['mobile-hamburger'].addEventListener('click', () => {
            if (++clickedCount >= 10) {
                elems['mobile-hamburger'].querySelector('i').style.display = 'none';
                elems['hamburger-img'].style.display = 'block';
            }
            elems['mobile-nav'].classList.toggle('show');
        });

        document.querySelectorAll('.nav-go-back').forEach(elem => {
            elem.addEventListener('click', () => history.back());
        });
    });
})();
