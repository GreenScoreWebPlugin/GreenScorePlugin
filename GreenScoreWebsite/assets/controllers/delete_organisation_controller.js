import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = ["modal"]

    openModal(event) {
        event.preventDefault()
        this.modalTarget.classList.remove('hidden')
        this.modalTarget.classList.add('flex')
    }

    closeModal() {
        this.modalTarget.classList.add('hidden')
        this.modalTarget.classList.remove('flex')
    }

    async handleDelete() {
        try {
            const response = await fetch('/api/organization/delete', {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const result = await response.json();
                if (result.redirect) {
                    window.location.href = "/logout";
                    window.location.href = result.redirect;
                }
            } else {
                const error = await response.json();
                console.error('Erreur:', error);
                // Gérer l'erreur ici
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    }
}