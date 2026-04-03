<?php
session_start();
$page_title = "Appointments";
$role = "receptionist";
$dashboard_name = "RECEPTIONIST DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../receptionist/receptionist_styling.css">';

// Schedule appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_appointment'])) {
    $vehicle_id = intval($_POST['vehicle_id']);
    $date       = $_POST['appointment_date'];
    $time       = $_POST['appointment_time'];

    // Ensure at least one service is selected
    if (empty($_POST['services'])) {
        $_SESSION['popup_message'] = "❌ You must select at least one service!";
        $_SESSION['popup_type'] = "error";
        header("Location: receptionist_appointments.php"); exit();
    }

    $services   = json_encode($_POST['services']);

    // Ensure only future dates
    if (strtotime($date) < strtotime(date("Y-m-d"))) {
        $_SESSION['popup_message'] = "❌ Appointment date must be in the future!";
        $_SESSION['popup_type'] = "error";
        header("Location: receptionist_appointments.php"); exit();
    }

    // ✅ Restrict appointment time to working hours (07:00–17:00)
    $timeCheck = strtotime($time);
    $startTime = strtotime("07:00");
    $endTime   = strtotime("17:00");

    if ($timeCheck < $startTime || $timeCheck > $endTime) {
        $_SESSION['popup_message'] = "❌ Appointment time must be between 7:00 AM and 5:00 PM!";
        $_SESSION['popup_type'] = "error";
        header("Location: receptionist_appointments.php"); exit();
    }

    $stmt = $conn->prepare("INSERT INTO appointments 
        (vehicle_id, appointment_date, appointment_time, services_selected, status, created_at)
        VALUES (?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("isss", $vehicle_id, $date, $time, $services);

    if ($stmt->execute()) {
        $_SESSION['popup_message'] = "✅ Appointment scheduled!";
        $_SESSION['popup_type'] = "success";
    } else {
        $_SESSION['popup_message'] = "❌ Error scheduling appointment!";
    $_SESSION['popup_type'] = "error";
    }
    header("Location: receptionist_appointments.php"); exit();
    
    
}

// Fetch vehicles
$vehicles = $conn->query("SELECT v.id, v.reg_number, u.username AS owner
                          FROM vehicles v
                          JOIN users u ON v.owner_id=u.id
                          ORDER BY v.created_at DESC");

// Fetch services
$services = $conn->query("SELECT id, service_name, price FROM services ORDER BY created_at DESC");

// Fetch appointments
$appointments = $conn->query("SELECT a.id, v.reg_number, a.appointment_date, a.appointment_time, a.services_selected, a.status, a.created_at
                              FROM appointments a
                              JOIN vehicles v ON a.vehicle_id=v.id
                              ORDER BY a.created_at DESC LIMIT 10");
?>

<main class="main-content">
    <h2 class="page-heading"><?= $page_title; ?></h2>

    <!-- Popup Messages -->
    <?php include_once("../public/includes/popup.php"); ?>

    <!-- Schedule Appointment Form -->
    <form method="POST" class="inline-form" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <!-- Vehicle Selection (searchable) -->
        <input list="vehicleList" name="vehicle_id" placeholder="Search Vehicle (Reg + Owner)" required>
        <datalist id="vehicleList">
            <?php while($v = $vehicles->fetch_assoc()): ?>
                <option value="<?= $v['id']; ?>"><?= $v['reg_number']; ?> (Owner: <?= htmlspecialchars($v['owner']); ?>)</option>
            <?php endwhile; ?>
        </datalist>

        <!-- Appointment Date -->
        <input type="date" name="appointment_date" min="<?= date('Y-m-d'); ?>" required>

        <!-- Appointment Time -->
        <input type="time" name="appointment_time" required>

        <!-- Services Dropdown with Search + Checkboxes -->
        <div class="dropdown">
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

        <button type="submit" name="schedule_appointment" class="btn-green">Schedule</button>

        <!-- Inline Search beside Schedule button -->
        <input type="text" id="searchAppointments" placeholder="Search appointments...">
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