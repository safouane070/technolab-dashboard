document.addEventListener('DOMContentLoaded', () => {
  // Live zoeken
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      const filter = searchInput.value.toLowerCase();
      const rows = document.querySelectorAll('#werknemerTable tbody tr');

      rows.forEach(row => {
        const naamCell = row.querySelector('.werknemer-naam');
        const naam = naamCell ? naamCell.textContent.toLowerCase() : '';
        row.style.display = naam.includes(filter) ? '' : 'none';
      });
    });
  }
});
