document.addEventListener('DOMContentLoaded', function () {
    const dropdownLang = document.getElementById('dropdownLang');
    const langIcon = document.getElementById('langIcon');
    const langDropdown = document.getElementById('dropdownlang');

    if (dropdownLang && langIcon && langDropdown) {
        // Saat dropdown dibuka
        langDropdown.addEventListener('shown.bs.dropdown', function () {
            langIcon.classList.remove('bi-chevron-down');
            langIcon.classList.add('bi-chevron-up');
        });

        // Saat dropdown ditutup
        langDropdown.addEventListener('hidden.bs.dropdown', function () {
            langIcon.classList.remove('bi-chevron-up');
            langIcon.classList.add('bi-chevron-down');
        });
    }
});
