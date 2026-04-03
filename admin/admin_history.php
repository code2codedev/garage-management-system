<?php
session_start();
$page_title = "System History";
$role = "admin";
$dashboard_name = "ADMIN DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
echo '<link rel="stylesheet" href="../admin/admin_styling.css">';
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");

// Latest history entries
$history = $conn->query("SELECT h.id, h.action, u.username AS performer, h.created_at
                         FROM history h
                         JOIN users u ON h.performed_by_id=u.id
                         ORDER BY h.created_at DESC LIMIT 20");
?>

<main class="main-content">
    <h2 class="page-heading"><?= $page_title; ?></h2>
    <div class="history-header">
        <h2>Latest History Entries</h2>

        <!-- Search Box -->
        <input type="text" id="searchHistory" placeholder="Search history..." style="margin-left:47%; width:250px;">

    </div>

    <div class="table-wrapper">
        <table class="styled-table" id="historyTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Action</th>
                    <th>Performed By</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $history->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['action']); ?></td>
                    <td><?= htmlspecialchars($row['performer']); ?></td>
                    <td><?= $row['created_at']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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