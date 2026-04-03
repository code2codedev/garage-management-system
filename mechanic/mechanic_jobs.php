<?php
session_start();
$page_title = "My Jobs";
$role = "mechanic";
$dashboard_name = "MECHANIC DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../mechanic/mechanic_styling.css">';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Update job status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_job'])) {
    $job_id  = intval($_POST['job_id']);
    $status  = trim($_POST['status']);
    $notes   = trim($_POST['notes']);

    $stmt = $conn->prepare("UPDATE jobs SET status=?, notes=? WHERE id=? AND mechanic_id=?");
    $stmt->bind_param("ssii", $status, $notes, $job_id, $_SESSION['user_id']);
    if ($stmt->execute()) {
        $conn->query("UPDATE appointments a JOIN jobs j ON a.id=j.appointment_id SET a.status='$status' WHERE j.id=$job_id");
        $conn->query("UPDATE vehicles v JOIN jobs j ON v.id=j.vehicle_id SET v.status='$status' WHERE j.id=$job_id");

        $action = "Updated job #$job_id to status '$status'";
        $log = $conn->prepare("INSERT INTO history (action, job_id, vehicle_id, performed_by_id, created_at) 
                               SELECT ?, j.id, j.vehicle_id, ?, NOW() FROM jobs j WHERE j.id=?");
        $log->bind_param("sii", $action, $_SESSION['user_id'], $job_id);
        $log->execute();

        $_SESSION['popup_message'] = "✅ Job updated!";
        $_SESSION['popup_type'] = "success";
    } else {
        $_SESSION['popup_message'] = "❌ Error updating job!";
        $_SESSION['popup_type'] = "error";
    }
    header("Location: mechanic_jobs.php"); exit();
}

// Mechanic sends message to receptionist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $job_id         = intval($_POST['job_id']);
    $mechanic_id    = $_SESSION['user_id'];
    $receptionist_id= intval($_POST['receptionist_id']);
    $message        = trim($_POST['message']);

    // Prevent messaging if job is completed
    $check = $conn->prepare("SELECT status FROM jobs WHERE id=? AND mechanic_id=?");
    $check->bind_param("ii", $job_id, $mechanic_id);
    $check->execute();
    $check->bind_result($current_status);
    $check->fetch();
    $check->close();

    if ($current_status === 'completed') {
        $_SESSION['popup_message'] = "❌ Cannot send message for a completed job!";
        $_SESSION['popup_type'] = "error";
        header("Location: mechanic_jobs.php"); exit();
    }

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, status, created_at) 
                            VALUES (?, ?, ?, 'unread', NOW())");
    $stmt->bind_param("iis", $mechanic_id, $receptionist_id, $message);

    if ($stmt->execute()) {
        $_SESSION['popup_message'] = "✅ Message sent to receptionist!";
        $_SESSION['popup_type'] = "success";
    } else {
        $_SESSION['popup_message'] = "❌ Error sending message!";
        $_SESSION['popup_type'] = "error";
    }
    header("Location: mechanic_jobs.php"); exit();
}

// Fetch jobs assigned to mechanic
$jobs = $conn->query("SELECT j.id, j.status, j.notes, j.vehicle_id, j.appointment_id,
                             v.reg_number, v.owner_id, j.created_at
                      FROM jobs j
                      JOIN vehicles v ON j.vehicle_id=v.id
                      WHERE j.mechanic_id=".$_SESSION['user_id']."
                      ORDER BY j.created_at DESC LIMIT 10");

// Fetch receptionists
$receptionists = $conn->query("SELECT id, username FROM users WHERE role='receptionist' ORDER BY username ASC");
?>

<main class="main-content">
    <div >
        <h2 class="page-heading"><?= $page_title; ?></h2>
    </div>

     <!-- Inline Search beside heading -->
        <input type="text" id="searchJobs" placeholder="Search jobs...">

    <?php include_once("../public/includes/popup.php"); ?>

    <div class="table-wrapper">
        <div class="table-container">
            <table class="styled-table" id="jobsTable">
                <thead>
                    <tr>
                        <th>ID</th><th>Vehicle</th><th>Services</th><th>Status</th>
                        <th>Admin Notes</th><th>Date</th><th>Actions</th><th>Send Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $jobs->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['reg_number']; ?></td>
                        <td>
                            <?php
                                $srvRes = $conn->query("SELECT services_selected FROM appointments WHERE id=".$row['appointment_id']." LIMIT 1");
                                if ($srvRow = $srvRes->fetch_assoc()) {
                                    $services_selected = json_decode($srvRow['services_selected'], true);
                                    if (!empty($services_selected)) {
                                        $formatted = [];
                                        foreach ($services_selected as $s) {
                                            $sObj = json_decode($s, true);
                                            if ($sObj) {
                                                $formatted[] = "(" . htmlspecialchars($sObj['name']) . ")";
                                            }
                                        }
                                        echo implode(" | ", $formatted);
                                    }
                                }
                            ?>
                        </td>
                        <td><?= ucfirst($row['status']); ?></td>
                        <td><?= htmlspecialchars($row['notes']); ?></td>
                        <td><?= $row['created_at']; ?></td>
                        <td>
                            <?php if ($row['status'] !== 'completed'): ?>
                            <form method="POST" class="inline-form">
                                <input type="hidden" name="job_id" value="<?= $row['id']; ?>">
                                <input type="hidden" name="notes" value="<?= htmlspecialchars($row['notes']); ?>">
                                <select name="status" required>
                                    <option value="in_progress" <?= $row['status']=='in_progress'?'selected':''; ?>>In Progress</option>
                                    <option value="completed" <?= $row['status']=='completed'?'selected':''; ?>>Completed</option>
                                </select>
                                <button type="submit" name="update_job" class="btn-blue">Update</button>
                            </form>
                            <?php else: ?>
                                <span class="locked">🔒 Locked</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['status'] !== 'completed'): ?>
                            <form method="POST" class="inline-form">
                                <input type="hidden" name="job_id" value="<?= $row['id']; ?>">
                                <textarea name="message" placeholder="Recommendation..." required></textarea>
                                <select name="receptionist_id" required>
                                    <?php
                                    $receptionists->data_seek(0);
                                    while($r = $receptionists->fetch_assoc()): ?>
                                        <option value="<?= $r['id']; ?>"><?= htmlspecialchars($r['username']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                                <button type="submit" name="send_message" class="btn-green">Send</button>
                            </form>
                            <?php else: ?>
                                <span class="locked">🔒 Locked</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="../public/js/global.js"></script>
<script>
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