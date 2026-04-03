<?php
session_start();
$page_title = "Manage Users";
$role = "admin";
$dashboard_name = "ADMIN DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../admin/admin_styling.css">';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = trim($_POST['role']);
    $status   = "active";

    try {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role, status, created_at) 
                                VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $username, $email, $password, $role, $status);
        $stmt->execute();

        $_SESSION['popup_message'] = "✅ User added successfully!";
        $_SESSION['popup_type'] = "success";
    } catch (mysqli_sql_exception $e) {
        if (strpos($e->getMessage(), 'Duplicate') !== false) {
            $_SESSION['popup_message'] = "❌ Username or Email already exists!";
            $_SESSION['popup_type'] = "error";
        } else {
            $_SESSION['popup_message'] = "❌ Error adding user: " . $e->getMessage();
            $_SESSION['popup_type'] = "error";
        }
    }
    header("Location: admin_users.php"); exit();
}

// Toggle user status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    $id = intval($_POST['user_id']);
    $current_status = $_POST['current_status'];
    $new_status = ($current_status === 'active') ? 'frozen' : 'active';

    $stmt = $conn->prepare("UPDATE users SET status=? WHERE id=?");
    $stmt->bind_param("si", $new_status, $id);

    if ($stmt->execute()) {
        $_SESSION['popup_message'] = "✅ User status updated to $new_status!";
        $_SESSION['popup_type'] = "success";
    } else {
        $_SESSION['popup_message'] = "❌ Error updating status!";
        $_SESSION['popup_type'] = "error";
    }
    header("Location: admin_users.php"); exit();
}

// Fetch users
$users = $conn->query("SELECT id, username, email, role, status, created_at FROM users ORDER BY created_at DESC");
?>

<main class="main-content">
    <h2 class="page-heading"><?= $page_title; ?></h2>

    <?php include_once("../public/includes/popup.php"); ?>

    <!-- Add User Form -->
    <form method="POST" class="inline-form">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="receptionist">Receptionist</option>
            <option value="mechanic">Mechanic</option>
            <option value="customer">Customer</option>
        </select>
        <button type="submit" name="add_user" class="btn-green">Add User</button>
    </form>

    <!-- Search Box -->
    <input type="text" id="searchUsers" placeholder="Search users..." style="margin-left:30px; width:120px">

    <!-- Users Table -->
    <div class="table-wrapper">
        <div class="table-container">
            <table class="styled-table" id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Date</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['username']); ?></td>
                        <td><?= htmlspecialchars($row['email']); ?></td>
                        <td><?= ucfirst($row['role']); ?></td>
                        <td><?= ucfirst($row['status']); ?></td>
                        <td><?= $row['created_at']; ?></td>
                        <td>
                            <form method="POST" class="inline-form">
                                <input type="hidden" name="user_id" value="<?= $row['id']; ?>">
                                <input type="hidden" name="current_status" value="<?= $row['status']; ?>">
                                <?php if ($row['status'] === 'active'): ?>
                                    <button type="submit" name="toggle_status" class="btn-red">Freeze</button>
                                <?php else: ?>
                                    <button type="submit" name="toggle_status" class="btn-green">Unfreeze</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
// Search functionality for Users
document.getElementById('searchUsers').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#usersTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<script src="../public/js/global.js"></script>
<?php include_once("../public/includes/footer.php"); ?>