document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("search");
    const rows = document.querySelectorAll("tbody tr");

    if (!searchInput) return;

    searchInput.addEventListener("input", () => {
        const filter = searchInput.value.toLowerCase();

        rows.forEach(row => {
            // tweede kolom bevat de naam (index 1)
            const naamCell = row.cells[1];
            if (!naamCell) return;

            const naam = naamCell.textContent.toLowerCase();
            row.style.display = naam.includes(filter) ? "" : "none";
        });
    });
});
