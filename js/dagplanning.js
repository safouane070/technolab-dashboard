document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("search");
    const rows = document.querySelectorAll("tbody tr");

    if (!searchInput || rows.length === 0) return;

    // Kleine delay om performance te verbeteren bij snel typen
    let timeout = null;

    searchInput.addEventListener("input", () => {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            const filter = searchInput.value.toLowerCase().trim();

            rows.forEach(row => {
                // ✅ Pak de eerste kolom (naam)
                const naamCell = row.querySelector("td:first-child");
                if (!naamCell) return;

                const naam = naamCell.textContent.toLowerCase();
                row.style.display = naam.includes(filter) ? "" : "none";
            });
        }, 150); // 150ms vertraging voor betere performance
    });
});
