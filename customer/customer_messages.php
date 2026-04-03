<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$page_title = "Messages";
$role = "customer";
$dashboard_name = "CUSTOMER DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../customer/customer_styling.css">';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Handle sending (customer sends message to receptionist)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $sender_id   = $_SESSION['user_id'];       // customer
    $receiver_id = intval($_POST['receiver_id']); // receptionist selected from form
    $message     = trim($_POST['message']);

    if ($sender_id == $receiver_id) {
        $_SESSION['popup_message'] = "❌ Cannot send to yourself.";
        $_SESSION['popup_type'] = "error";
        header("Location: customer_messages.php");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, status, created_at)
                            VALUES (?, ?, ?, 'unread', NOW())");
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    $stmt->execute();
    $stmt->close();

    $_SESSION['popup_message'] = "✅ Message sent!";
    $_SESSION['popup_type'] = "success";
    header("Location: customer_messages.php");
    exit();
}

// Fetch receptionists
$users = $conn->query("SELECT id, username FROM users WHERE role='receptionist' ORDER BY username ASC");

// Fetch recent messages involving this customer
$messages = $conn->query("SELECT m.*, u.username AS sender_name, u2.username AS receiver_name
                          FROM messages m
                          JOIN users u ON m.sender_id=u.id
                          JOIN users u2 ON m.receiver_id=u2.id
                          WHERE m.sender_id=".$_SESSION['user_id']." 
                             OR m.receiver_id=".$_SESSION['user_id']."
                          ORDER BY m.created_at DESC 
                          LIMIT 20");
?>

<main class="main-content">
    <div >
        <h2 class="page-heading"><?= $page_title; ?></h2>
    </div>

    <!-- Send Message Form -->
    <form method="POST" class="inline-form" style="margin:15px 0;">
        <select name="receiver_id" required>
            <option value="">Select Receptionist</option>
            <?php while($r = $users->fetch_assoc()): ?>
                <option value="<?= $r['id']; ?>"><?= htmlspecialchars($r['username']); ?></option>
            <?php endwhile; ?>
        </select>
        <textarea name="message" placeholder="Type your message..." required></textarea>
        <button type="submit" name="send_message" class="btn-green">Send</button>
         <!-- Inline Search beside heading -->
        <input type="text" id="searchMessages" placeholder="Search messages..." >
    </form>

    <!-- Messages Table -->
    <div class="table-wrapper">
        <div class="table-container">
            <table class="styled-table" id="messagesTable">
                <thead><tr><th>ID</th><th>Sender</th><th>Receiver</th><th>Message</th><th>Date</th></tr></thead>
                <tbody>
                    <?php while($row = $messages->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['sender_name']); ?></td>
                        <td><?= htmlspecialchars($row['receiver_name']); ?></td>
                        <td><?= htmlspecialchars($row['message']); ?></td>
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
// Search functionality for Messages
document.getElementById('searchMessages').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#messagesTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<?php include_once("../public/includes/footer.php"); ?>