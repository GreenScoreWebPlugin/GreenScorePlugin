document.addEventListener('DOMContentLoaded', function() {
    console.log('Cookie consent script loaded');
    
    function setupCookieConsent() {
        console.log('Setting up cookie consent');
        const acceptButton = document.getElementById('acceptCookies');
        
        console.log('Accept button:', acceptButton);
        
        if (acceptButton) {
            // Supprimer les écouteurs existants
            const newButton = acceptButton.cloneNode(true);
            acceptButton.parentNode.replaceChild(newButton, acceptButton);
            
            newButton.addEventListener('click', function(e) {
                console.log('Cookie consent button clicked');
                
                e.preventDefault();
                e.stopPropagation();
                
                fetch('/cookie/consent', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Consent response received');
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
        } else {
            console.log('No accept button found');
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