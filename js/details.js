document.querySelectorAll('.btn-details').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;

        fetch('details.php?id=' + id)
            .then(response => response.text())
            .then(data => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');
                const bodyContent = doc.querySelector('body').innerHTML;

                // Modal vullen + bewerk knop toevoegen
                document.getElementById('modal-content').innerHTML = `
                ${bodyContent}
                <div class="modal-actions">
                    <a href="update.php?id=${id}">
                        <button class="btn btn-primary">Bewerken</button>
                    </a>
                </div>
            `;
                document.getElementById('detail-modal').style.display = 'flex';
            });
    });
});

// Sluiten
document.getElementById('close-modal').addEventListener('click', () => {
    document.getElementById('detail-modal').style.display = 'none';
});

window.addEventListener('click', e => {
    if(e.target.id === 'detail-modal'){
        document.getElementById('detail-modal').style.display = 'none';
    }
});
