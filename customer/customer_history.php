<?php
session_start();
$page_title = "Customer Dashboard";
$role = "customer";
$dashboard_name = "CUSTOMER DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../customer/customer_styling.css">';

// Ensure only customers can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../public/login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];

// Fetch completed & paid appointments for this customer
$history = $conn->query("
    SELECT v.id AS vehicle_id,
           v.reg_number,
           a.id AS appointment_id,
           j.id AS job_id,
           j.status AS job_status,
           p.status AS payment_status,
           p.amount,
           p.method,
           j.created_at AS completed_date
    FROM jobs j
    JOIN appointments a ON j.appointment_id = a.id
    JOIN vehicles v ON a.vehicle_id = v.id
    JOIN payments p ON p.appointment_id = a.id
    WHERE v.owner_id = $customer_id
      AND j.status = 'completed'
      AND p.status = 'paid'
    ORDER BY j.created_at DESC
");
?>

<main class="main-content">
    <div >
        <h2 class="page-heading"><?= $page_title; ?></h2>
    </div>
     <!-- Inline Search beside heading -->
        <input type="text" id="searchHistory" placeholder="Search history...">

    <div class="table-wrapper">
        <table class="styled-table" id="historyTable">
            <thead>
                <tr>
                    <th>Vehicle ID</th>
                    <th>Reg Number</th>
                    <th>Appointment ID</th>
                    <th>Job ID</th>
                    <th>Service Status</th>
                    <th>Payment Status</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Date Completed</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $history->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['vehicle_id']; ?></td>
                    <td><?= htmlspecialchars($row['reg_number']); ?></td>
                    <td><?= $row['appointment_id']; ?></td>
                    <td><?= $row['job_id']; ?></td>
                    <td><span style="color:green;"><?= ucfirst($row['job_status']); ?></span></td>
                    <td><span style="color:blue;"><?= ucfirst($row['payment_status']); ?></span></td>
                    <td><?= number_format($row['amount'],2); ?></td>
                    <td><?= ucfirst($row['method']); ?></td>
                    <td><?= $row['completed_date']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<script src="../public/js/global.js"></script>
<script>
// Search functionality for History
document.getElementById('searchHistory').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#historyTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<style>
.table-wrapper {
    max-height: 220px; /* about 7 rows visible */
    overflow-y: auto;
}
.styled-table {
    width: 100%;
    border-collapse: collapse;
}
.styled-table th, .styled-table td {
    padding: 8px;
    border: 1px solid #ddd;
    text-align: left;
}
.styled-table thead {
    background: #f4f4f4;
    position: sticky;
    top: 0;
    z-index: 2;
}
.styled-table tr:nth-child(even) {
    background: #fafafa;
}
</style>

<?php include_once("../public/includes/footer.php"); ?>