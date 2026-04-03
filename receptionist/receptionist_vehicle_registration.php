<?php
session_start();
$page_title = "Vehicle Registration";
$role = "receptionist";
$dashboard_name = "RECEPTIONIST DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../receptionist/receptionist_styling.css">';

// Register vehicle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_vehicle'])) {
    $reg_number = strtoupper(trim($_POST['reg_number']));
    $owner_id   = intval($_POST['owner_id']);
    $phone      = trim($_POST['phone']);

    // Kenyan vehicle registration format: LLL NNNL (e.g., KDQ 437L)
    if (!preg_match("/^[A-Z]{3}\s[0-9]{3}[A-Z]$/", $reg_number)) {
        $_SESSION['popup_message'] = "❌ Invalid vehicle registration format! Use format: LLL NNNL (e.g., KDQ 437L)";
        $_SESSION['popup_type'] = "error";
        header("Location: receptionist_vehicle_registration.php"); exit();
    }

    // Kenyan phone format: 07xx xxx xxx or 01xx xxx xxx
    if (!preg_match("/^(07\d{8}|01\d{8})$/", $phone)) {
        $_SESSION['popup_message'] = "❌ Invalid phone number format! Use format: 07xx xxx xxx or 01xx xxx xxx";
        $_SESSION['popup_type'] = "error";
        header("Location: receptionist_vehicle_registration.php"); exit();
    }

    // Prevent duplicate registration for same owner
    $check = $conn->prepare("SELECT id FROM vehicles WHERE reg_number=? AND owner_id=?");
    $check->bind_param("si", $reg_number, $owner_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['popup_message'] = "❌ Vehicle already registered for this owner!";
        $_SESSION['popup_type'] = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO vehicles (reg_number, owner_id, phone, created_at)
                                VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sis", $reg_number, $owner_id, $phone);

        if ($stmt->execute()) {
            $_SESSION['popup_message'] = "✅ Vehicle registered!";
            $_SESSION['popup_type'] = "success";
        } else {
            $_SESSION['popup_message'] = "❌ Error registering vehicle!";
            $_SESSION['popup_type'] = "error";
        }
    }
    header("Location: receptionist_vehicle_registration.php"); exit();
}

// Fetch customers
$customers = $conn->query("SELECT id, username FROM users WHERE role='customer' ORDER BY username ASC");

// Fetch vehicles
$vehicles = $conn->query("SELECT v.id, v.reg_number, u.username AS owner, v.phone, v.created_at
                          FROM vehicles v
                          JOIN users u ON v.owner_id=u.id
                          ORDER BY v.created_at DESC LIMIT 10");
?>

<main class="main-content">
    <h2 class="page-heading"><?= $page_title; ?></h2>
    <?php include_once("../public/includes/popup.php"); ?>

    <!-- Register Vehicle Form -->
    <form method="POST" class="inline-form">
        <input type="text" name="reg_number" placeholder="Reg Number (e.g. KDQ 437L)" required>

        <!-- Searchable input + dropdown for customer names -->
        <input list="customerList" name="owner_id" placeholder="Search Owner Name" required>
        <datalist id="customerList">
            <?php while($c = $customers->fetch_assoc()): ?>
                <option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['username']); ?></option>
            <?php endwhile; ?>
        </datalist>

        <input type="text" name="phone" placeholder="Phone Number (e.g. 0712345678)" required>
        <button type="submit" name="register_vehicle" class="btn-green">Register Vehicle</button>

        <!-- Inline Search beside Register Vehicle button -->
        <input type="text" id="searchVehicles" placeholder="Search vehicles...">
    </form>

    <!-- Vehicles Table -->
    <div class="table-wrapper">
        <div class="table-container">
            <table class="styled-table" id="vehiclesTable">
                <thead>
                    <tr><th>ID</th><th>Reg Number</th><th>Owner</th><th>Phone</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php while($row = $vehicles->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['reg_number']); ?></td>
                        <td><?= htmlspecialchars($row['owner']); ?></td>
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

<?php include_once("../public/includes/footer.php"); ?>