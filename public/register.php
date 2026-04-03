<?php
session_start();
include_once("includes/db_connect.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = "customer";

    $check = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "❌ Username or Email already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $password, $role);
        $success = $stmt->execute()
          ? "✅ Registration successful. You can now login."
          : "❌ Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="form-container">
    <img src="images/l1 (1).jpeg" alt="Logo" class="logo">
    <h2 class="orange-text">Create Account</h2>
    <?= isset($error) ? "<div class='message-box error'>$error</div>" : "" ?>
    <?= isset($success) ? "<div class='message-box success'>$success</div>" : "" ?>
    <form method="POST" class="form-box">
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" class="btn-green">Register</button>
    </form>
    <p><a href="login.php" class="orange-link">Back to Login</a></p>
  </div>
</body>
</html>