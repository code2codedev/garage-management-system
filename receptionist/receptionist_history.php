<?php
session_start();
$page_title = "Receptionist History";
$role = "receptionist";
$dashboard_name = "RECEPTIONIST DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");

// Clear history
if (isset($_POST['clear_history'])) {
    $stmt = $conn->prepare("DELETE FROM history WHERE performed_by_id=?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $_SESSION['popup_message'] = "🗑️ Receptionist history cleared!";
    $_SESSION['popup_type'] = "success";
    header("Location: receptionist_history.php"); exit();
}

// Latest history entries
$history = $conn->query("SELECT h.id, h.action, u.username AS performer, h.created_at
                         FROM history h
                         JOIN users u ON h.performed_by_id=u.id
                         WHERE u.role='receptionist'
                         ORDER BY h.created_at DESC LIMIT 20");
?>

<main class="main-content">
    <h2 class="page-heading"><?= $page_title; ?></h2>
    <form method="POST" onsubmit="return confirm('Clear receptionist history?');">
        <button type="submit" name="clear_history" class="btn-red">Clear History</button>
    </form>

    <h2>Latest History Entries</h2>
    <div class="table-wrapper">
        <div class="table-container">
            <table class="styled-table">
                <thead><tr><th>ID</th><th>Action</th><th>Performed By</th><th>Date</th></tr></thead>
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
    </div>
</main>

<script src="../public/js/global.js"></script>
<?php include_once("../public/includes/footer.php"); ?>