import { Controller } from "stimulus";

export default class extends Controller {
    static targets = ["clipboard", "checkIcon", "codeText"];

    handleCopy() {
        var clipboard = this.clipboardTarget;
        var checkIcon = this.checkIconTarget;
        var codeText = this.codeTextTarget;
        navigator.clipboard.writeText(codeText.textContent).then(function() {
            clipboard.classList.add('invisible');
            checkIcon.classList.remove('invisible');
            setTimeout(function() {
                clipboard.classList.remove('invisible');
                checkIcon.classList.add('invisible');
            }, 5000);
        }).catch(function(err) {
            console.error('Failed to copy: ', err);
        });
    }
}