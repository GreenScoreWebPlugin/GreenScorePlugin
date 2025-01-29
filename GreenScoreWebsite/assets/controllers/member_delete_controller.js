import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['modal', 'deleteButton']

    connect() {
        this.modalTarget.addEventListener('click', (e) => {
            if (e.target === this.modalTarget) {
                this.closeModal();
            }
        });
    }

    openModal(event) {
        this.currentMemberId = event.currentTarget.dataset.memberId;
        this.modalTarget.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    closeModal() {
        this.modalTarget.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    async deleteMember() {
        try {
            const response = await fetch(`/gerer-organisation/membres/${this.currentMemberId}/delete`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                // Récupérer les paramètres actuels
                const urlParams = new URLSearchParams(window.location.search);
                let currentPage = parseInt(urlParams.get('page')) || 1;
                const searchTerm = urlParams.get('search') || '';

                // Récupérer d'abord la liste mise à jour pour vérifier le nombre total de pages
                const checkResponse = await fetch(`/gerer-organisation/membres?page=${currentPage}&search=${searchTerm}`);
                const checkHtml = await checkResponse.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(checkHtml, 'text/html');

                // Vérifier si la page actuelle est vide
                const members = doc.querySelectorAll('[data-controller="copy-clipboard"] .flex.py-3');
                if (members.length === 0 && currentPage > 1) {
                    // Si la page est vide et ce n'est pas la première page, reculer d'une page
                    currentPage--;
                    // Mettre à jour l'URL
                    const newUrl = new URL(window.location.href);
                    newUrl.searchParams.set('page', currentPage);
                    window.history.pushState({}, '', newUrl);
                }

                // Charger la nouvelle page
                const reloadResponse = await fetch(`/gerer-organisation/membres?page=${currentPage}&search=${searchTerm}`);
                const html = await reloadResponse.text();
                const newDoc = parser.parseFromString(html, 'text/html');

                // Mettre à jour le contenu
                const container = document.querySelector('[data-controller="copy-clipboard"]');
                const newContent = newDoc.querySelector('[data-controller="copy-clipboard"]');

                if (container && newContent) {
                    container.innerHTML = newContent.innerHTML;
                }
            } else {
                console.error('Erreur lors de la suppression');
            }
        } catch (error) {
            console.error('Erreur:', error);
        } finally {
            this.closeModal();
        }
    }
}