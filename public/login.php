<?php
session_start();
include_once("includes/db_connect.php"); // DB connection in /public/includes

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password_hash, role, status FROM users WHERE username=? OR email=?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password_hash'])) {
            if ($row['status'] === 'frozen') {
                $error = "❌ Account frozen. You cannot access the dashboard.";
            } else {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role']    = $row['role'];

                switch ($row['role']) {
                    case 'admin':
                        header("Location: ../admin/admin_dashboard.php");
                        break;
                    case 'receptionist':
                        header("Location: ../receptionist/receptionist_dashboard.php");
                        break;
                    case 'mechanic':
                        header("Location: ../mechanic/mechanic_dashboard.php");
                        break;
                    case 'customer':
                        header("Location: ../customer/customer_dashboard.php");
                        break;
                }
                exit();
            }
        } else {
            $error = "❌ Invalid password.";
        }
    } else {
        $error = "❌ User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <!-- CSS path relative to /public -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="form-container">
    <!-- Image path relative to /public -->
    <img src="images/l1 (1).jpeg" alt="Logo" class="logo">
    <h2 class="orange-text">Login</h2>
    <?= isset($error) ? "<div class='message-box error'>$error</div>" : "" ?>
    <form method="POST" class="form-box">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" class="btn-green">Login</button>
    </form>
    <p><a href="register.php" class="orange-link">Create Account</a></p>
  </div>
</body>
</html>