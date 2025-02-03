document.getElementById('copy-button').addEventListener('click', function() {
    var codeText = document.getElementById('code-text').textContent;
    var copyButton = document.getElementById('copy-button');
    var checkIcon = document.getElementById('check-icon');

    navigator.clipboard.writeText(codeText).then(function() {
        setTimeout(function() {
            copyButton.classList.add('invisible');
            checkIcon.classList.remove('invisible');
            checkIcon.classList.add('animate-pulse');
        }, 5000);
    }).catch(function(err) {
        console.error('Failed to copy: ', err);
    });
});