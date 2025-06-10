document.addEventListener('DOMContentLoaded', function () {
    const dropdownLang = document.getElementById('dropdownLang');
    const langIcon = document.getElementById('langIcon');

    if (dropdownLang && langIcon) {
        dropdownLang.addEventListener('show.bs.dropdown', function () {
            langIcon.classList.add('rotate-up');
        });

        dropdownLang.addEventListener('hide.bs.dropdown', function () {
            langIcon.classList.remove('rotate-up');
        });
    }
});

    function adjustMainContentOffset() {
        const navbar = document.getElementById('mainNavbar');
        const mainContent = document.querySelector('.main-content');
        const navbarHeight = navbar.offsetHeight;

        mainContent.style.marginTop = navbarHeight + 'px';
    }

    // Panggil saat halaman dimuat dan saat jendela diubah ukurannya
    window.addEventListener('load', adjustMainContentOffset);
    window.addEventListener('resize', adjustMainContentOffset);
