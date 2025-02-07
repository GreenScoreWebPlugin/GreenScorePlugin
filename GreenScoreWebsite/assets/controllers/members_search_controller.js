import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ['input', 'membersContainer'];
    static values = {
        url: String
    }

    connect() {
        // Ajouter un écouteur pour la touche Entrée
        this.inputTarget.addEventListener('keypress', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                this.handleSearch();
            }
        });
    }

    handleSearch() {
        const searchTerm = this.inputTarget.value.trim();
        const searchUrl = new URL(window.location.href);
        searchUrl.searchParams.set('search', searchTerm);
        searchUrl.searchParams.set('page', '1'); // Réinitialiser à la première page lors d'une nouvelle recherche

        // Rediriger vers l'URL avec le terme de recherche
        window.location.href = searchUrl.toString();
    }
}