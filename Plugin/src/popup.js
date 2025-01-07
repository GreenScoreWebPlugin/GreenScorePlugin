document.addEventListener('DOMContentLoaded', () => {
    function getColorClass(gCO2e) {
        const value = Number(gCO2e);
        
        if (value <= 75) {
            return {
                text: 'text-[#4CAF50]',
                bg: 'bg-[#4CAF50]',
                border: 'border-[#4CAF50]'
            };
        } else if (value <= 150) {
            return {
                text: 'text-[#FFC107]',
                bg: 'bg-[#FFC107]',
                border: 'border-[#FFC107]'
            };
        } else if (value <= 225) {
            return {
                text: 'text-[#FF9800]',
                bg: 'bg-[#FF9800]',
                border: 'border-[#FF9800]'
            };
        } else {
            return {
                text: 'text-[#BD2525]',
                bg: 'bg-[#FFB7B7]',
                border: 'border-[#BD2525]'
            };
        }
    }

    function updateColors(gCO2e) {
        const colorClasses = getColorClass(gCO2e);
        
        const gCO2eContainer = document.getElementById('gCO2e-container');
        const gCO2eValue = document.getElementById('gCO2e-value');
        
        if (gCO2eContainer && gCO2eValue) {
            gCO2eContainer.className = `flex items-baseline font-outfit ${colorClasses.text}`;
            gCO2eValue.textContent = `${gCO2e}\u00A0`;
        }

        const comparisonCards = document.querySelectorAll('.comparison-card');
        comparisonCards.forEach(card => {
            card.className = `comparison-card flex flex-col p-2 w-[120px] h-[120px] ${colorClasses.bg} text-red-500 gap-2 border ${colorClasses.border} rounded-[4px]`;
            console.log(card);
        });
    }

    browser.runtime.sendMessage({ type: 'getCountryAndUrl' }).then((response) => {
        if (response && response.country && response.url) {
            const urlElement = document.getElementById('site-url');
            const countryElement = document.getElementById('site-country');
            
            if (countryElement && urlElement) {
                countryElement.textContent = `Dans votre pays (${response.country}), cette page consomme`;
                urlElement.textContent = response.url;
            }
        } else if (response.error) {
            console.error('Erreur :', response.error);
        }
    }).catch((error) => {
        console.error("Erreur lors de la récupération du pays ou de l'URL :", error);
    });

    browser.runtime.sendMessage({ type: 'getgCO2e' }).then((response) => {
        if (response && typeof response.gCO2e === 'number') {
            updateColors(response.gCO2e);
        } else {
            console.error('Valeur gCO2e invalide:', response);
        }
    }).catch((error) => {
        console.error('Erreur lors de la récupération du gCO2e :', error);
    });
});