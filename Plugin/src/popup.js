document.addEventListener('DOMContentLoaded', () => {
    browser.runtime.sendMessage({ type: 'getCountryAndUrl' }).then((response) => {
        if (response && response.country && response.url) {
            const urlElement = document.querySelector('#site-url');
            const countryElement = document.querySelector('#site-country');
            if (countryElement && urlElement) {
                countryElement.textContent = `Dans votre pays (${response.country}), cette page consomme`;
                urlElement.textContent = `${response.url}`;
            }
        } else if (response.error) {
            console.error('Erreur :', response.error);
        }
    }).catch((error) => {
        console.error('Erreur lors de la récupération du pays ou de l’URL :', error);
    });
});
