document.addEventListener('DOMContentLoaded', function() {

    function setupCookieConsent() {
        const acceptButton = document.getElementById('acceptCookies');
        
        if (acceptButton) {
            // Supprimer les écouteurs existants
            const newButton = acceptButton.cloneNode(true);
            acceptButton.parentNode.replaceChild(newButton, acceptButton);
            
            newButton.addEventListener('click', function(e) {

                e.preventDefault();
                e.stopPropagation();
                
                fetch('/cookie/consent', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    const cookieConsent = document.getElementById('cookieConsent');
                    if (cookieConsent) {
                        cookieConsent.style.transform = 'translateY(100%)';
                        setTimeout(() => {
                            cookieConsent.style.display = 'none';
                        }, 500);
                    }
                })
                .catch(error => {
                    console.error('Cookie consent error:', error);
                });
            });
        }
    }
    
    // Initialisation
    setupCookieConsent();
    
    // Ajout d'écouteurs pour différents types de navigation
    if (document.addEventListener) {
        document.addEventListener('turbo:load', setupCookieConsent);
        window.addEventListener('popstate', setupCookieConsent);
    }
});