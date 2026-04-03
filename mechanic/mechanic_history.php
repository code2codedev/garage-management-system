<?php
session_start();
$page_title = "Mechanic History";
$role = "mechanic";
$dashboard_name = "MECHANIC DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../mechanic/mechanic_styling.css">';

// Fetch history
$history = $conn->query("SELECT h.id, h.action, v.reg_number, h.created_at
                         FROM history h
                         LEFT JOIN vehicles v ON h.vehicle_id=v.id
                         WHERE h.performed_by_id=".$_SESSION['user_id']."
                         ORDER BY h.created_at DESC LIMIT 20");
?>

<main class="main-content">
    <div>
        <h2 class="page-heading"><?= $page_title; ?></h2>
    </div>
<div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
    <h2>Latest History Entries</h2>
    <!-- Inline Search beside heading -->
        <input type="text" id="searchHistory" placeholder="Search history...">
</div>
    <div class="table-wrapper">
        <div class="table-container">
            <table class="styled-table" id="historyTable">
                <thead><tr><th>ID</th><th>Action</th><th>Vehicle</th><th>Date</th></tr></thead>
                <tbody>
                    <?php while($row = $history->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['action']); ?></td>
                        <td><?= htmlspecialchars($row['reg_number']); ?></td>
                        <td><?= $row['created_at']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="../public/js/global.js"></script>
<script>
// Search functionality for History
document.getElementById('searchHistory').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#historyTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<?php include_once("../public/includes/footer.php"); ?>