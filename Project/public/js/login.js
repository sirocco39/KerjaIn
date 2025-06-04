function openLogin() {
    document.getElementById('loginModal').classList.remove('hidden');
    document.getElementById('registerModal').classList.add('hidden');
}

function closeLogin() {
    document.getElementById('loginModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    // Ambil elemen yang diperlukan
    const masukBtn = document.getElementById('masukBtn');
    const loginModal = document.getElementById('loginModal');
    const overlay = document.createElement('div');
    overlay.className = 'overlay';
    document.body.appendChild(overlay);

    // Ketika tombol Masuk diklik - cegah default behavior link
    masukBtn.addEventListener('click', function(e) {
        e.preventDefault(); // Mencegah navigasi ke href="#"
        loginModal.style.display = 'block';
        overlay.style.display = 'block';
    });

    // Fungsi untuk menutup modal
    function closeModal() {
        loginModal.style.display = 'none';
        overlay.style.display = 'none';
    }

    // Ketika klik di overlay
    overlay.addEventListener('click', closeModal);

    // Ketika tekan ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
});