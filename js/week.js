document.addEventListener("DOMContentLoaded", () => {
    // Modal open/close
    window.openStatusModal = function (id, dag) {
        document.getElementById(`modal-${id}-${dag}`).style.display = "block";
    };

    window.closeStatusModal = function (id, dag) {
        document.getElementById(`modal-${id}-${dag}`).style.display = "none";
    };

    // Klik buiten modal om te sluiten
    window.onclick = function (event) {
        const modals = document.querySelectorAll(".status-modal");
        modals.forEach((modal) => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    };
});
