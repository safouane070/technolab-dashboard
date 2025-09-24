document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("search");
    const rows = document.querySelectorAll("tbody tr");

    if (searchInput) {
        searchInput.addEventListener("keyup", () => {
            const filter = searchInput.value.toLowerCase();

            rows.forEach(row => {
                const naam = row.cells[0].textContent.toLowerCase();
                row.style.display = naam.includes(filter) ? "" : "none";
            });
        });
    }
});
