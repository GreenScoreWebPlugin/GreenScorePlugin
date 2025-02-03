// Sélection des éléments nécessaires
const burgerButton = document.getElementById('burger-button');
const mobileMenu = document.getElementById('mobile-menu');
const profileButton = document.getElementById('profile-button');
const profileMenu = document.getElementById('profile-menu');

// Fonction pour gérer le menu burger
burgerButton?.addEventListener('click', (event) => {
    event.stopPropagation(); // Empêche la propagation pour éviter la fermeture immédiate
    mobileMenu.classList.toggle('hidden'); // Bascule l'état du menu mobile

    // Assure que le menu profil est fermé
    profileMenu?.classList.add('hidden');
});

// Fonction pour gérer le menu profil
profileButton?.addEventListener('click', (event) => {
    event.stopPropagation(); // Empêche la propagation pour éviter la fermeture immédiate
    profileMenu.classList.toggle('hidden'); // Bascule l'état du menu profil

    // Assure que le menu burger est fermé
    mobileMenu?.classList.add('hidden');
});

// Gestion des clics en dehors des menus pour les fermer
document.addEventListener('click', () => {
    mobileMenu?.classList.add('hidden');
    profileMenu?.classList.add('hidden');
});

// Gestion des clics sur les liens des menus
const menuLinks = document.querySelectorAll('#mobile-menu a, #profile-menu a');
menuLinks.forEach((link) => {
    link.addEventListener('click', (event) => {
        event.stopPropagation(); // Empêche la propagation pour ne pas déclencher la fermeture immédiate
        mobileMenu?.classList.add('hidden'); // Ferme le menu mobile
        profileMenu?.classList.add('hidden'); // Ferme le menu profil
    });
});