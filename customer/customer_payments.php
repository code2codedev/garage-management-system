<?php
session_start();
$page_title = "My Payments";
$role = "customer";
$dashboard_name = "CUSTOMER DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../customer/customer_styling.css">';

$user_id = $_SESSION['user_id'] ?? 0;

// Fetch all appointments + payments for this customer’s vehicles
$payments = $conn->query("
    SELECT a.id AS appointment_id,
           v.reg_number,
           a.services_selected,
           p.amount,
           p.method,
           p.status AS payment_status,
           p.created_at
    FROM appointments a
    JOIN vehicles v ON a.vehicle_id = v.id
    LEFT JOIN payments p ON p.appointment_id = a.id
    WHERE v.owner_id = $user_id
    ORDER BY a.created_at DESC
");
?>

<main class="main-content">
    <div >
        <h2 class="page-heading"><?= $page_title; ?></h2>
    </div>
     <!-- Inline Search beside heading -->
        <input type="text" id="searchPayments" placeholder="Search payments...">

    <?php include_once("../public/includes/popup.php"); ?>

    <div class="table-wrapper">
        <div class="table-container">
            <table class="styled-table" id="paymentsTable">
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Vehicle Reg</th>
                        <th>Total Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $payments->fetch_assoc()): ?>
                    <?php
                        // Calculate total from services_selected JSON
                        $total_amount = 0;
                        if (!empty($row['services_selected'])) {
                            $services_selected = json_decode($row['services_selected'], true);
                            if (is_array($services_selected)) {
                                foreach ($services_selected as $s) {
                                    $sObj = is_string($s) ? json_decode($s, true) : $s;
                                    if ($sObj && isset($sObj['price'])) {
                                        $total_amount += floatval($sObj['price']);
                                    }
                                }
                            }
                        }
                        // Optional: add fixed service fee if part of your business logic
                        $service_fee = 300;
                        $total_amount += $service_fee;
                    ?>
                    <tr>
                        <td><?= $row['appointment_id']; ?></td>
                        <td><?= htmlspecialchars($row['reg_number']); ?></td>
                        <td><?= number_format($total_amount,2); ?></td>
                        <td><?= $row['method'] ? ucfirst($row['method']) : '-'; ?></td>
                        <td><?= $row['payment_status'] ? ucfirst($row['payment_status']) : 'unpaid'; ?></td>
                        <td><?= $row['created_at'] ? $row['created_at'] : '-'; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="../public/js/global.js"></script>
<script>
// Search functionality for Payments
document.getElementById('searchPayments').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#paymentsTable tbody tr');
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