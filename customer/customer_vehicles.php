<?php
session_start();
$page_title = "My Vehicles";
$role = "customer";
$dashboard_name = "CUSTOMER DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../customer/customer_styling.css">';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Register vehicle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_vehicle'])) {
    $reg_number = strtoupper(trim($_POST['reg_number']));
    $phone      = trim($_POST['phone']);
    $owner_id   = $_SESSION['user_id']; // customer is the owner

    // Kenyan vehicle registration format: LLL NNNL (e.g., KDQ 437L)
    if (!preg_match("/^[A-Z]{3}\s[0-9]{3}[A-Z]$/", $reg_number)) {
        $_SESSION['popup_message'] = "❌ Invalid vehicle registration format! Use format: LLL NNNL (e.g., KDQ 437L)";
        $_SESSION['popup_type'] = "error";
        header("Location: customer_vehicles.php"); exit();
    }

    // Kenyan phone format: 07xx xxx xxx or 01xx xxx xxx
    if (!preg_match("/^(07\d{8}|01\d{8})$/", $phone)) {
        $_SESSION['popup_message'] = "❌ Invalid phone number format! Use format: 07XXXXXXXX or 01XXXXXXXX";
        $_SESSION['popup_type'] = "error";
        header("Location: customer_vehicles.php"); exit();
    }

    // Prevent duplicate registration for same owner
    $check = $conn->prepare("SELECT id FROM vehicles WHERE reg_number=? AND owner_id=?");
    $check->bind_param("si", $reg_number, $owner_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['popup_message'] = "❌ Vehicle already registered!";
        $_SESSION['popup_type'] = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO vehicles (reg_number, owner_id, phone, created_at)
                                VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sis", $reg_number, $owner_id, $phone);

        if ($stmt->execute()) {
            $_SESSION['popup_message'] = "✅ Vehicle registered successfully!";
            $_SESSION['popup_type'] = "success";
        } else {
            $_SESSION['popup_message'] = "❌ Error registering vehicle!";
            $_SESSION['popup_type'] = "error";
        }
    }
    header("Location: customer_vehicles.php"); exit();
}

// Fetch vehicles
$vehicles = $conn->query("SELECT id, reg_number, phone, created_at
                          FROM vehicles WHERE owner_id=".$_SESSION['user_id']."
                          ORDER BY created_at DESC");
?>

<main class="main-content">
    <div >
        <h2 class="page-heading"><?= $page_title; ?></h2>
    </div>

    <?php include_once("../public/includes/popup.php"); ?>

    <!-- Register Vehicle Form -->
    <form method="POST" class="inline-form">
        <input type="text" name="reg_number" placeholder="Reg Number (e.g. KDQ 437L)" required>
        <input type="text" name="phone" placeholder="Phone Number (07XXXXXXXX or 01XXXXXXXX)" required>
        <button type="submit" name="register_vehicle" class="btn-green">Register Vehicle</button>
        <!-- Inline Search beside heading -->
        <input type="text" id="searchVehicles" placeholder="Search vehicles...">
    </form>

    <!-- Vehicles Table -->
    <div class="table-wrapper">
        <div class="table-container">
            <table class="styled-table" id="vehiclesTable">
                <thead>
                    <tr><th>ID</th><th>Reg Number</th><th>Phone</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php while($row = $vehicles->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['reg_number']); ?></td>
                        <td><?= htmlspecialchars($row['phone']); ?></td>
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
// Search functionality for Vehicles
document.getElementById('searchVehicles').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#vehiclesTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<style>
.table-wrapper { max-height: 220px; overflow-y: auto; }
.styled-table { width: 100%; border-collapse: collapse; }
.styled-table th, .styled-table td { padding: 8px; border: 1px solid #ddd; text-align: left; }
.styled-table thead { background: #f4f4f4; position: sticky; top: 0; z-index: 2; }
.styled-table tr:nth-child(even) { background: #fafafa; }
</style>

<?php include_once("../public/includes/footer.php"); ?>