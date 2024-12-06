// Burger Menu
const burgerButton = document.getElementById('burger-button');
const mobileMenu = document.getElementById('mobile-menu');

burgerButton.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
});

// Profile Menu
const profileButton = document.getElementById('profile-button');
const profileMenu = document.getElementById('profile-menu');

profileButton.addEventListener('click', () => {
    profileMenu.classList.toggle('hidden');
});

// Fermer le menu profil si on clique ailleurs
document.addEventListener('click', (event) => {
    if (!profileButton.contains(event.target) && !profileMenu.contains(event.target)) {
        profileMenu.classList.add('hidden');
    }
});