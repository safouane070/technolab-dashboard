document.querySelectorAll('.btn-details').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        fetch('details.php?id=' + id)
        .then(response => response.text())
        .then(data => {
            // Extract alleen de inhoud van de <body> van details.php
            const parser = new DOMParser();
            const doc = parser.parseFromString(data, 'text/html');
            const bodyContent = doc.querySelector('body').innerHTML;
            document.getElementById('modal-content').innerHTML = bodyContent;
            document.getElementById('detail-modal').style.display = 'flex';
        });
    });
});

document.getElementById('close-modal').addEventListener('click', () => {
    document.getElementById('detail-modal').style.display = 'none';
});

window.addEventListener('click', e => {
    if(e.target.id === 'detail-modal'){
        document.getElementById('detail-modal').style.display = 'none';
    }
});