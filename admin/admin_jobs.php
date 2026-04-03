<?php
session_start();
$page_title = "Job Scheduling";
$role = "admin";
$dashboard_name = "ADMIN DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../admin/admin_styling.css">';

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Assign job
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_job'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $vehicle_id     = intval($_POST['vehicle_id']);
    $mechanic_id    = intval($_POST['mechanic_id']);
    $notes          = trim($_POST['notes']); // Admin note

    $stmt = $conn->prepare("INSERT INTO jobs (appointment_id, vehicle_id, mechanic_id, status, notes, created_at) 
                            VALUES (?, ?, ?, 'in_progress', ?, NOW())");
    $stmt->bind_param("iiis", $appointment_id, $vehicle_id, $mechanic_id, $notes);

    if ($stmt->execute()) {
        $conn->query("UPDATE appointments SET status='in_progress' WHERE id=$appointment_id");
        $conn->query("UPDATE vehicles SET status='in_progress' WHERE id=$vehicle_id");

        $_SESSION['popup_message'] = "✅ Job assigned!";
        $_SESSION['popup_type'] = "success";
    } else {
        $_SESSION['popup_message'] = "❌ Error assigning job!";
        $_SESSION['popup_type'] = "error";
    }
    header("Location: admin_jobs.php"); exit();
}

// Fetch pending appointments
$appointments = $conn->query("SELECT a.id, a.vehicle_id, v.reg_number, u.username AS owner, u.id AS owner_id,
                                     a.appointment_date, a.appointment_time, a.status
                              FROM appointments a
                              JOIN vehicles v ON a.vehicle_id=v.id
                              JOIN users u ON v.owner_id=u.id
                              WHERE a.status='pending'
                              ORDER BY a.created_at DESC");

// Fetch mechanics
$mechanics = $conn->query("SELECT id, username FROM users WHERE role='mechanic' ORDER BY username ASC");

// Fetch jobs
$jobs = $conn->query("SELECT j.id, j.notes, v.reg_number, u.username AS mechanic, j.status, j.created_at
                      FROM jobs j
                      JOIN vehicles v ON j.vehicle_id=v.id
                      JOIN users u ON j.mechanic_id=u.id
                      ORDER BY j.created_at DESC LIMIT 10");
?>

<main class="main-content">
    <h2 class="page-heading"><?= $page_title; ?></h2>

    <!-- Popup Messages -->
    <?php include_once("../public/includes/popup.php"); ?>

    <!-- Assign Job Form -->
    <form method="POST" class="inline-form">
        <select name="appointment_id" required onchange="updateVehicleId(this)">
            <option value="">Select Appointment</option>
            <?php while($a = $appointments->fetch_assoc()): ?>
                <option value="<?= $a['id']; ?>" data-vehicle="<?= $a['vehicle_id']; ?>" data-owner="<?= $a['owner_id']; ?>">
                    <?= $a['reg_number']; ?> (Owner: <?= htmlspecialchars($a['owner']); ?>, <?= $a['appointment_date']; ?> <?= $a['appointment_time']; ?>)
                </option>
            <?php endwhile; ?>
        </select>
        <input type="hidden" name="vehicle_id" id="vehicle_id">
        <input type="hidden" name="owner_id" id="owner_id">

        <select name="mechanic_id" required>
            <option value="">Select Mechanic</option>
            <?php while($m = $mechanics->fetch_assoc()): ?>
                <option value="<?= $m['id']; ?>"><?= htmlspecialchars($m['username']); ?></option>
            <?php endwhile; ?>
        </select>
        <input type="text" name="notes" placeholder="Admin note to mechanic">
        <button type="submit" name="assign_job" class="btn-green">Assign Job</button>
        <!-- Search Box -->
    <input type="text" id="searchJobs" placeholder="Search jobs..." style="margin-left:20px; width:120px;">

    </form>

    <!-- Jobs Table -->
    <div class="table-wrapper">
        <div class="table-container">
            <table class="styled-table" id="jobsTable">
                <thead>
                    <tr>
                        <th>ID</th><th>Vehicle</th><th>Mechanic</th><th>Status</th><th>Date</th><th>Admin Note</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $jobs->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['reg_number']; ?></td>
                        <td><?= htmlspecialchars($row['mechanic']); ?></td>
                        <td><?= ucfirst($row['status']); ?></td>
                        <td><?= $row['created_at']; ?></td>
                        <td><?= htmlspecialchars($row['notes']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="../public/js/global.js"></script>
<script>
function updateVehicleId(select) {
    var vehicleId = select.options[select.selectedIndex].getAttribute('data-vehicle');
    var ownerId = select.options[select.selectedIndex].getAttribute('data-owner');
    document.getElementById('vehicle_id').value = vehicleId;
    document.getElementById('owner_id').value = ownerId;
}

// Search functionality for Jobs
document.getElementById('searchJobs').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#jobsTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<?php include_once("../public/includes/footer.php"); ?>