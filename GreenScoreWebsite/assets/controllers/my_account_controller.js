// assets/controllers/my_account_controller.js
import { Controller } from "stimulus";

export default class extends Controller {
    static targets = ["modal"];

    // Cache la modal à la connexion du contrôleur
    connect() {
        this.modalTarget.classList.add("hidden");
    }

    // Affiche la modal en enlevant la classe 'hidden'
    openModal() {
        this.modalTarget.classList.remove("hidden");
    }

    // Ferme la modal en ajoutant la classe 'hidden'
    closeModal() {
        this.modalTarget.classList.add("hidden");
    }

    // permet à l'utilisateur de quitter l'organisation
    leaveOrga() {
        this.modalTarget.classList.add("hidden");
    }
}