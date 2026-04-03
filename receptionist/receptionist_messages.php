<?php
session_start();
$page_title = "Messages";
$role = "receptionist";
$dashboard_name = "RECEPTIONIST DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../receptionist/receptionist_styling.css">';

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiver_id = intval($_POST['receiver_id']);
    $message     = trim($_POST['message']);

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $_SESSION['user_id'], $receiver_id, $message);

    if ($stmt->execute()) {
        $_SESSION['popup_message'] = "✅ Message sent!";
        $_SESSION['popup_type'] = "success";
    } else {
        $_SESSION['popup_message'] = "❌ Error sending message!";
        $_SESSION['popup_type'] = "error";
    }
    header("Location: receptionist_messages.php"); exit();
}

// Fetch customers
$customers = $conn->query("SELECT id, username FROM users WHERE role='customer' ORDER BY username ASC");

// Fetch messages
$messages = $conn->query("SELECT m.id, u.username AS sender, u2.username AS receiver, m.message, m.created_at
                          FROM messages m
                          JOIN users u ON m.sender_id=u.id
                          JOIN users u2 ON m.receiver_id=u2.id
                          ORDER BY m.created_at DESC LIMIT 20");
?>

<main class="main-content">
    <h2 class="page-heading"><?= $page_title; ?></h2>
    <?php include_once("../public/includes/popup.php"); ?>

    <!-- Send Message Form -->
    <form method="POST" class="inline-form">
        <select name="receiver_id" required>
            <?php while($c = $customers->fetch_assoc()): ?>
                <option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['username']); ?></option>
            <?php endwhile; ?>
        </select>
        <textarea name="message" placeholder="Type your message..." required></textarea>
        <button type="submit" name="send_message" class="btn-green">Send</button>

        <!-- Inline Search beside Send button -->
        <input type="text" id="searchMessages" placeholder="Search messages...">
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
                        <td><?= htmlspecialchars($row['sender']); ?></td>
                        <td><?= htmlspecialchars($row['receiver']); ?></td>
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