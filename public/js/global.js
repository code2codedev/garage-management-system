document.addEventListener("DOMContentLoaded", function() {
    let toggleBtn = document.getElementById("toggleRowsBtn");
    let rows = document.querySelectorAll("#usersTable tbody tr");

    if (toggleBtn && rows.length > 0) {
        // Collapse to 4 rows initially
        rows.forEach((row, index) => {
            if (index >= 4) row.style.display = "none";
        });

        toggleBtn.addEventListener("click", function() {
            let expanded = this.getAttribute("data-expanded") === "true";

            if (!expanded) {
                // Show all rows
                rows.forEach(row => row.style.display = "");
                this.textContent = "View Less";
                this.setAttribute("data-expanded", "true");
            } else {
                // Hide back to 4 rows
                rows.forEach((row, index) => {
                    row.style.display = index < 4 ? "" : "none";
                });
                this.textContent = "View More";
                this.setAttribute("data-expanded", "false");
            }
        });
    }
});

// Simple search function
function searchTable() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let rows = document.querySelectorAll("#usersTable tbody tr");
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}