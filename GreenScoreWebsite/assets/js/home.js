document.addEventListener('turbo:load', function() {
    const container = document.getElementById('browser-plugin-container');
    const isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;

    if (!isFirefox) {
        container.innerHTML = `
            <a class="inline-flex items-center bg-white text-gs-green-950 font-outfit font-regular p-3 rounded-full mx-auto shadow-md shadow-black opacity-50 cursor-not-allowed relative group">
                <img src="/images/firefox.png" alt="Firefox" class="mr-2 h-6 w-6">
                Ajouter à Firefox
            </a>
        `;
    }
});

document.addEventListener('turbo:render', function() {
    const container = document.getElementById('browser-plugin-container');
    const isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;

    if (!isFirefox) {
        container.innerHTML = `
            <a class="inline-flex items-center bg-white text-gs-green-950 font-outfit font-regular p-3 rounded-full mx-auto shadow-md shadow-black opacity-50 cursor-not-allowed relative group">
                <img src="/images/firefox.png" alt="Firefox" class="mr-2 h-6 w-6">
                Ajouter à Firefox
            </a>
        `;
    }
});