<?php
session_start();
$page_title = "My Appointments";
$role = "customer";
$dashboard_name = "CUSTOMER DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../customer/customer_styling.css">';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Request appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_appointment'])) {
    $vehicle_id = intval($_POST['vehicle_id']);
    $date       = $_POST['appointment_date'];
    $time       = $_POST['appointment_time'];

    // Ensure at least one service is selected
    if (empty($_POST['services'])) {
        $_SESSION['popup_message'] = "❌ You must select at least one service!";
        $_SESSION['popup_type'] = "error";
        header("Location: customer_appointments.php"); exit();
    }

    $services   = json_encode($_POST['services']);

    // Validate ownership
    $checkVehicle = $conn->prepare("SELECT id FROM vehicles WHERE id=? AND owner_id=?");
    $checkVehicle->bind_param("ii", $vehicle_id, $_SESSION['user_id']);
    $checkVehicle->execute();
    $checkVehicle->store_result();

    if ($checkVehicle->num_rows === 0) {
        $_SESSION['popup_message'] = "❌ Invalid vehicle selection!";
        $_SESSION['popup_type'] = "error";
        header("Location: customer_appointments.php"); exit();
    }

    // Ensure only future dates
    if (strtotime($date) < strtotime(date("Y-m-d"))) {
        $_SESSION['popup_message'] = "❌ Appointment date must be in the future!";
        $_SESSION['popup_type'] = "error";
        header("Location: customer_appointments.php"); exit();
    }

    $stmt = $conn->prepare("INSERT INTO appointments 
        (vehicle_id, appointment_date, appointment_time, services_selected, status, created_at)
        VALUES (?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("isss", $vehicle_id, $date, $time, $services);

    if ($stmt->execute()) {
        $_SESSION['popup_message'] = "✅ Appointment requested!";
        $_SESSION['popup_type'] = "success";
    } else {
        $_SESSION['popup_message'] = "❌ Error requesting appointment!";
        $_SESSION['popup_type'] = "error";
    }
    header("Location: customer_appointments.php"); exit();
}

// Fetch vehicles
$vehicles = $conn->query("SELECT id, reg_number FROM vehicles WHERE owner_id=".$_SESSION['user_id']." ORDER BY created_at DESC");

// Fetch services
$services = $conn->query("SELECT id, service_name, price FROM services ORDER BY created_at DESC");

// Fetch appointments
$appointments = $conn->query("SELECT a.id, v.reg_number, a.appointment_date, a.appointment_time, a.services_selected, a.status, a.created_at
                              FROM appointments a
                              JOIN vehicles v ON a.vehicle_id=v.id
                              WHERE v.owner_id=".$_SESSION['user_id']."
                              ORDER BY a.created_at DESC LIMIT 10");
?>

<main class="main-content">
    <h2 class="page-heading"><?= $page_title; ?></h2>
    <?php include_once("../public/includes/popup.php"); ?>

    <!-- Request Appointment Form -->
    <form method="POST" class="inline-form" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <select name="vehicle_id" required>
            <option value="">Select Vehicle</option>
            <?php while($v = $vehicles->fetch_assoc()): ?>
                <option value="<?= $v['id']; ?>"><?= $v['reg_number']; ?></option>
            <?php endwhile; ?>
        </select>

        <input type="date" name="appointment_date" min="<?= date('Y-m-d'); ?>" required>
        <input type="time" name="appointment_time" required>

        <div class="dropdown" style="display:flex;">
            <button type="button" class="btn-blue dropdown-toggle" onclick="toggleServices()">Select Services</button>
            <div id="servicesDropdown" class="dropdown-menu" style="display:none; border:1px solid #ccc; padding:10px; max-height:250px; overflow-y:auto;">
                <input type="text" id="serviceSearch" placeholder="Search services..." onkeyup="filterServices()" style="width:100%; margin-bottom:10px; padding:5px;">
                <div class="services-grid" id="servicesList">
                    <?php while($s = $services->fetch_assoc()): ?>
                        <label class="dropdown-item">
                            <input type="checkbox" name="services[]"  
                                   value='{"name":"<?= htmlspecialchars($s['service_name']); ?>","price":<?= $s['price']; ?>}'>
                            <?= htmlspecialchars($s['service_name']); ?> (<?= number_format($s['price'],2); ?>)
                        </label>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <button type="submit" name="request_appointment" class="btn-green">Request Appointment</button>
<div>
    <!-- Inline Search beside Request Appointment button -->
        <input type="text" id="searchAppointments" placeholder="Search appointments..." style="margin-left:0px; width:150px;display:flex;">

</div>
        
    </form>

    <!-- Appointments Table -->
    <div class="table-wrapper">
        <div class="table-container">
            <table class="styled-table" id="appointmentsTable">
                <thead><tr><th>ID</th><th>Vehicle</th><th>Date</th><th>Time</th><th>Services</th><th>Status</th><th>Created</th></tr></thead>
                <tbody>
                    <?php while($row = $appointments->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['reg_number']; ?></td>
                        <td><?= $row['appointment_date']; ?></td>
                        <td><?= $row['appointment_time']; ?></td>
                        <td>
                            <?php 
                                $services_selected = json_decode($row['services_selected'], true);
                                if (!empty($services_selected)) {
                                    foreach ($services_selected as $s) {
                                        $sObj = json_decode($s, true);
                                        if ($sObj) {
                                            echo htmlspecialchars($sObj['name'])." (".$sObj['price'].")<br>";
                                        }
                                    }
                                }
                            ?>
                        </td>
                        <td><?= ucfirst($row['status']); ?></td>
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
function toggleServices() {
    var dropdown = document.getElementById("servicesDropdown");
    dropdown.style.display = (dropdown.style.display === "none") ? "block" : "none";
}
function filterServices() {
    var input = document.getElementById("serviceSearch");
    var filter = input.value.toLowerCase();
    var items = document.querySelectorAll("#servicesList label");
    items.forEach(function(item) {
        var text = item.textContent.toLowerCase();
        item.style.display = text.includes(filter) ? "block" : "none";
    });
}

// Search functionality for Appointments
document.getElementById('searchAppointments').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#appointmentsTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<style>
.services-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.dropdown-item { display: flex; align-items: center; }
</style>

<?php include_once("../public/includes/footer.php"); ?>