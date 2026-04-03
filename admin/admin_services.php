<?php
session_start();
$page_title = "Services Management";
$role = "admin";
$dashboard_name = "ADMIN DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../admin/admin_styling.css">';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Add service
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $name  = trim($_POST['service_name']);
    $price = floatval($_POST['price']);

    $check = $conn->prepare("SELECT id FROM services WHERE service_name = ?");
    $check->bind_param("s", $name);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['popup_message'] = "⚠️ Service name already exists!";
        $_SESSION['popup_type'] = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO services (service_name, price, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("sd", $name, $price);

        if ($stmt->execute()) {
            $_SESSION['popup_message'] = "✅ Service added!";
            $_SESSION['popup_type'] = "success";
        } else {
            $_SESSION['popup_message'] = "❌ Error adding service!";
            $_SESSION['popup_type'] = "error";
        }
    }
    header("Location: admin_services.php"); exit();
}

// Update service
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_service'])) {
    $id    = intval($_POST['service_id']);
    $name  = trim($_POST['service_name']);
    $price = floatval($_POST['price']);

    $check = $conn->prepare("SELECT id FROM services WHERE service_name = ? AND id != ?");
    $check->bind_param("si", $name, $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['popup_message'] = "⚠️ Another service with this name already exists!";
        $_SESSION['popup_type'] = "error";
    } else {
        $stmt = $conn->prepare("UPDATE services SET service_name=?, price=? WHERE id=?");
        $stmt->bind_param("sdi", $name, $price, $id);

        if ($stmt->execute()) {
            $_SESSION['popup_message'] = "✅ Service updated!";
            $_SESSION['popup_type'] = "success";
        } else {
            $_SESSION['popup_message'] = "❌ Error updating service!";
            $_SESSION['popup_type'] = "error";
        }
    }
    header("Location: admin_services.php"); exit();
}

// Fetch services
$services = $conn->query("SELECT id, service_name, price, created_at FROM services ORDER BY created_at DESC");
?>

<main class="main-content">
    <h2 class="page-heading"><?= $page_title; ?></h2>

    <?php include_once("../public/includes/popup.php"); ?>

    <!-- Add Service Form -->
    <form method="POST" class="inline-form">
        <input type="text" name="service_name" placeholder="Service Name" required>
        <input type="number" step="0.01" name="price" placeholder="Price" required>
        <button type="submit" name="add_service" class="btn-green">Add Service</button>
    </form>

    <!-- Search Box -->
    <input type="text" id="searchServices" placeholder="Search services..." style="margin-left:200px; width:250px;">

    <!-- Services Table -->
    <div class="table-wrapper">
        <div class="table-container">
            <table class="styled-table" id="servicesTable">
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Price</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php while($row = $services->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['service_name']); ?></td>
                        <td><?= number_format($row['price'],2); ?></td>
                        <td><?= $row['created_at']; ?></td>
                        <td>
                            <form method="POST" class="inline-form">
                                <input type="hidden" name="service_id" value="<?= $row['id']; ?>">
                                <input type="text" name="service_name" value="<?= htmlspecialchars($row['service_name']); ?>" required>
                                <input type="number" step="0.01" name="price" value="<?= $row['price']; ?>" required>
                                <button type="submit" name="update_service" class="btn-blue">Update</button>
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
// Search functionality for Services
document.getElementById('searchServices').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#servicesTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<script src="../public/js/global.js"></script>
<?php include_once("../public/includes/footer.php"); ?>