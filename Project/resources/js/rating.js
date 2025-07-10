
document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.star-rating');
    const ratingInput = document.getElementById('rating-input');
    let selectedRating = 0;

    stars.forEach(star => {
        // Hover effect
        star.addEventListener('mouseover', function () {
            const val = parseInt(this.getAttribute('data-value'));
            stars.forEach(s => {
                const sVal = parseInt(s.getAttribute('data-value'));
                if (sVal <= val) {
                    s.classList.add('star-green');
                    s.classList.remove('text-secondary');
                } else {
                    s.classList.remove('star-green');
                    s.classList.remove('star-blue');
                    s.classList.add('text-secondary');
                }
            });
        });

        // Remove green on mouseout (but keep blue selection)
        star.addEventListener('mouseout', function () {
            stars.forEach(s => {
                s.classList.remove('star-green');
                if (parseInt(s.getAttribute('data-value')) <= selectedRating) {
                    s.classList.add('star-blue');
                    s.classList.remove('text-secondary');
                } else {
                    s.classList.remove('star-blue');
                    s.classList.add('text-secondary');
                }
            });
        });

        // Click to select rating
        star.addEventListener('click', function () {
            selectedRating = parseInt(this.getAttribute('data-value'));
            ratingInput.value = selectedRating;
            stars.forEach(s => {
                if (parseInt(s.getAttribute('data-value')) <= selectedRating) {
                    s.classList.add('star-blue');
                    s.classList.remove('text-secondary');
                } else {
                    s.classList.remove('star-blue');
                    s.classList.add('text-secondary');
                }
            });
        });
    });
});