console.log('test');

document.getElementById('copy-button').addEventListener('click', function() {
    var codeText = document.getElementById('code-text').textContent;
    console.log(codeText);

    navigator.clipboard.writeText(codeText).then(function() {
        alert('Code copié dans le presse-papier!');
    }).catch(function(err) {
        alert('Échec de la copie: ' + err);
    });
});