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
