import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = ["modalLeaveOrga", "modalChangeOrga", "codeOrganisation", "helpText"]
    static values = {
        leaveUrl: String,
        changeUrl: String,
        leaveToken: String,
        changeToken: String
    }
    openModal(event) {
        const modalName = event.currentTarget.dataset.myAccountModalParam

        try {
            const modalTarget = this[`${modalName}Target`]
            modalTarget.classList.remove("hidden")
        } catch (error) {
            console.error("Error in openModal:", error)
        }
    }

    closeModal(event) {
        const modalName = event.currentTarget.dataset.myAccountModalParam
        const modalTarget = this[`${modalName}Target`]

        if (modalTarget) {
            modalTarget.classList.add("hidden")
        }
    }

    async leaveOrga(event) {
        const response = await fetch('/remove-organisation', {
            method: 'POST',
            headers: {
                'Accept': 'application/json'

            }
        })

        console.log(response);

        const data = await response.json()

        if (data.success) {
            this.modalLeaveOrgaTarget.classList.add("hidden")
            // Redirection immédiate
            window.location.href = '/mon-compte/organisation'  // ou votre route de redirection
        } else {
            throw new Error(data.error || 'Une erreur est survenue')
        }
    }

    async changeOrJoinOrga(event) {
        event.preventDefault();

        const codeOrganisationValue = this.codeOrganisationTarget.value;

        // Référence au conteneur d'erreur
        const errorContainer = this.codeOrganisationTarget.closest('div').querySelector('.error-message');

        // Validation du champ côté client
        if (codeOrganisationValue.length !== 8) {
            if (errorContainer) {
                errorContainer.textContent = 'Le code organisation doit contenir exactement 8 caractères.';
                errorContainer.classList.remove('hidden');
                this.helpTextTarget.classList.add('hidden');
            }
            return;
        }

        try {
            // Envoi de la requête au serveur
            const response = await fetch('/mon-compte/organisation/exist', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ code: codeOrganisationValue }),
            });

            const result = await response.json();

            // Gestion du succès ou de l'échec
            if (result.success) {
                // Soumettre le formulaire
                this.codeOrganisationTarget.closest('form').submit();
                errorContainer.classList.add('hidden');
                this.helpTextTarget.classList.remove('hidden');
            } else {
                if (errorContainer) {
                    errorContainer.textContent = result.errorMessage || 'Organisation non trouvée. Veuillez vérifier le code.';
                    errorContainer.classList.remove('hidden');
                    this.helpTextTarget.classList.add('hidden');
                }
            }
        } catch (error) {
            if (errorContainer) {
                errorContainer.textContent = error.message || 'Une erreur inattendue est survenue.';
                errorContainer.classList.remove('hidden');
                this.helpTextTarget.classList.add('hidden');
            }
        }
    }
}