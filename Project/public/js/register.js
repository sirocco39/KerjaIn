function openRegister() {
    document.getElementById('registerModal').classList.remove('hidden');
    document.getElementById('loginModal').classList.add('hidden');
}

function closeRegister() {
    document.getElementById('registerModal').classList.add('hidden');
}