fetch('details.php?id=' + id)
  .then(response => response.text())
  .then(data => {
    const parser = new DOMParser();
    const doc = parser.parseFromString(data, 'text/html');
    const card = doc.querySelector('.card');
    if (card) {
      card.classList.add('detail-card');
      document.getElementById('modal-content').innerHTML = card.outerHTML;
      document.getElementById('detail-modal').style.display = 'flex';
    }
  });
