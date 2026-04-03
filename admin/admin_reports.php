<?php
session_start();
$page_title = "Reports";
$role = "admin";
$dashboard_name = "ADMIN DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../admin/admin_styling.css">';

// Vehicles report
$vehicles = $conn->query("SELECT v.id, v.reg_number, u.username AS owner, v.status, v.payment_status, v.created_at
                          FROM vehicles v
                          JOIN users u ON v.owner_id=u.id
                          ORDER BY v.created_at DESC");

// Appointments report
$appointments = $conn->query("SELECT a.id, v.reg_number, a.appointment_date, a.appointment_time, a.status, a.created_at
                              FROM appointments a
                              JOIN vehicles v ON a.vehicle_id=v.id
                              ORDER BY a.created_at DESC");

// Jobs report
$jobs = $conn->query("SELECT j.id, v.reg_number, u.username AS mechanic, j.status, j.created_at
                      FROM jobs j
                      JOIN vehicles v ON j.vehicle_id=v.id
                      JOIN users u ON j.mechanic_id=u.id
                      ORDER BY j.created_at DESC");

// Payments report
$payments = $conn->query("SELECT p.id, a.id AS appointment_id, v.reg_number, u.username AS owner, 
                                 p.amount, p.method, p.status, p.created_at
                          FROM payments p
                          JOIN appointments a ON p.appointment_id=a.id
                          JOIN vehicles v ON a.vehicle_id=v.id
                          JOIN users u ON v.owner_id=u.id
                          ORDER BY p.created_at DESC");

// Inventory alerts
$alerts = $conn->query("SELECT id, item_name, price, instock, used, remaining, updated_at 
                        FROM inventory 
                        WHERE remaining <= 5 
                        ORDER BY remaining ASC");
?>

<main class="main-content">
    <h2 class="page-heading"><?= $page_title; ?></h2>

    <!-- Report Links + Search Bar -->
    <div class="report-header">
        <nav class="report-links">
            <button onclick="showReport('vehicles')">Vehicles Report</button>
            <button onclick="showReport('appointments')">Appointments Report</button>
            <button onclick="showReport('jobs')">Jobs Report</button>
            <button onclick="showReport('payments')">Payments Report</button>
            <button onclick="showReport('alerts')">Inventory Alerts</button>
            <input type="text" id="reportSearch" placeholder="Search current report..." style="margin-left:40px; width:160px;">
        </nav>
    </div>

    <!-- Vehicles Report -->
    <div id="vehicles" class="report-section" style="display:block;">
        <h3>Vehicles Report</h3>
        <div class="table-wrapper">
            <table class="styled-table" id="vehiclesTable">
                <thead>
                    <tr><th>ID</th><th>Reg Number</th><th>Owner</th><th>Status</th><th>Payment</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php while($v = $vehicles->fetch_assoc()): ?>
                    <tr>
                        <td><?= $v['id']; ?></td>
                        <td><?= htmlspecialchars($v['reg_number']); ?></td>
                        <td><?= htmlspecialchars($v['owner']); ?></td>
                        <td><?= ucfirst($v['status']); ?></td>
                        <td><?= ucfirst($v['payment_status']); ?></td>
                        <td><?= $v['created_at']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Appointments Report -->
    <div id="appointments" class="report-section" style="display:none;">
        <h3>Appointments Report</h3>
        <div class="table-wrapper">
            <table class="styled-table" id="appointmentsTable">
                <thead>
                    <tr><th>ID</th><th>Vehicle</th><th>Date</th><th>Time</th><th>Status</th><th>Created</th></tr>
                </thead>
                <tbody>
                    <?php while($a = $appointments->fetch_assoc()): ?>
                    <tr>
                        <td><?= $a['id']; ?></td>
                        <td><?= $a['reg_number']; ?></td>
                        <td><?= $a['appointment_date']; ?></td>
                        <td><?= $a['appointment_time']; ?></td>
                        <td><?= ucfirst($a['status']); ?></td>
                        <td><?= $a['created_at']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Jobs Report -->
    <div id="jobs" class="report-section" style="display:none;">
        <h3>Jobs Report</h3>
        <div class="table-wrapper">
            <table class="styled-table" id="jobsTable">
                <thead>
                    <tr><th>ID</th><th>Vehicle</th><th>Mechanic</th><th>Status</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php while($j = $jobs->fetch_assoc()): ?>
                    <tr>
                        <td><?= $j['id']; ?></td>
                        <td><?= $j['reg_number']; ?></td>
                        <td><?= htmlspecialchars($j['mechanic']); ?></td>
                        <td><?= ucfirst($j['status']); ?></td>
                        <td><?= $j['created_at']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payments Report -->
    <div id="payments" class="report-section" style="display:none;">
        <h3>Payments Report</h3>
        <div class="table-wrapper">
            <table class="styled-table" id="paymentsTable">
                <thead>
                    <tr><th>ID</th><th>Appointment</th><th>Vehicle</th><th>Owner</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php while($p = $payments->fetch_assoc()): ?>
                    <tr>
                        <td><?= $p['id']; ?></td>
                        <td><?= $p['appointment_id']; ?></td>
                        <td><?= $p['reg_number']; ?></td>
                        <td><?= htmlspecialchars($p['owner']); ?></td>
                        <td><?= number_format($p['amount'],2); ?></td>
                        <td><?= ucfirst($p['method']); ?></td>
                        <td><?= ucfirst($p['status']); ?></td>
                        <td><?= $p['created_at']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Inventory Alerts -->
    <div id="alerts" class="report-section" style="display:none;">
        <h3>Inventory Alerts</h3>
        <div class="table-wrapper">
            <table class="styled-table" id="alertsTable">
                <thead>
                    <tr><th>ID</th><th>Item</th><th>Price</th><th>Instock</th><th>Used</th><th>Remaining</th><th>Updated</th><th>Alert</th></tr>
                </thead>
                <tbody>
                    <?php while($i = $alerts->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i['id']; ?></td>
                        <td><?= htmlspecialchars($i['item_name']); ?></td>
                        <td><?= number_format($i['price'],2); ?></td>
                        <td><?= $i['instock']; ?></td>
                        <td><?= $i['used']; ?></td>
                        <td><?= $i['remaining']; ?></td>
                        <td><?= $i['updated_at']; ?></td>
                        <td>
                            <?php if ($i['remaining'] == 0): ?>
                                <span style="color:red;">❌ Out of Stock</span>
                            <?php else: ?>
                                <span style="color:orange;">⚠️ Low Stock</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ✅ Export Button -->
    <div style="position:fixed; bottom:20px; left:20px;">
        <button onclick="exportReportToCSV()">Export Current Report to CSV</button>
    </div>
</main>

<script src="../public/js/global.js"></script>
<script>
function showReport(id) {
    document.querySelectorAll('.report-section').forEach(sec => sec.style.display = 'none');
    document.getElementById(id).style.display = 'block';
    currentReport = id; // track active report
}

// Track active report (default is Vehicles)
let currentReport = "vehicles";

// Unified search functionality
document.getElementById('reportSearch').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let tableId = currentReport + "Table";
    let rowsHere’s the **finished Reports page code** with the CSV export button and complete JavaScript logic. Everything else is unchanged — I’ve only added the export functionality at the bottom left:

```php
<?php
session_start();
$page_title = "Reports";
$role = "admin";
$dashboard_name = "ADMIN DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../admin/admin_styling.css">';

// Vehicles report
$vehicles = $conn->query("SELECT v.id, v.reg_number, u.username AS owner, v.status, v.payment_status, v.created_at
                          FROM vehicles v
                          JOIN users u ON v.owner_id=u.id
                          ORDER BY v.created_at DESC");

// Appointments report
$appointments = $conn->query("SELECT a.id, v.reg_number, a.appointment_date, a.appointment_time, a.status, a.created_at
                              FROM appointments a
                              JOIN vehicles v ON a.vehicle_id=v.id
                              ORDER BY a.created_at DESC");

// Jobs report
$jobs = $conn->query("SELECT j.id, v.reg_number, u.username AS mechanic, j.status, j.created_at
                      FROM jobs j
                      JOIN vehicles v ON j.vehicle_id=v.id
                      JOIN users u ON j.mechanic_id=u.id
                      ORDER BY j.created_at DESC");

// Payments report
$payments = $conn->query("SELECT p.id, a.id AS appointment_id, v.reg_number, u.username AS owner, 
                                 p.amount, p.method, p.status, p.created_at
                          FROM payments p
                          JOIN appointments a ON p.appointment_id=a.id
                          JOIN vehicles v ON a.vehicle_id=v.id
                          JOIN users u ON v.owner_id=u.id
                          ORDER BY p.created_at DESC");

// Inventory alerts
$alerts = $conn->query("SELECT id, item_name, price, instock, used, remaining, updated_at 
                        FROM inventory 
                        WHERE remaining <= 5 
                        ORDER BY remaining ASC");
?>

<main class="main-content">
    <h2 class="page-heading"><?= $page_title; ?></h2>

    <!-- Report Links + Search Bar -->
    <div class="report-header">
        <nav class="report-links">
            <button onclick="showReport('vehicles')">Vehicles Report</button>
            <button onclick="showReport('appointments')">Appointments Report</button>
            <button onclick="showReport('jobs')">Jobs Report</button>
            <button onclick="showReport('payments')">Payments Report</button>
            <button onclick="showReport('alerts')">Inventory Alerts</button>
            <input type="text" id="reportSearch" placeholder="Search current report..." style="margin-left:40px; width:160px;">
        </nav>
    </div>

    <!-- Vehicles Report -->
    <div id="vehicles" class="report-section" style="display:block;">
        <h3>Vehicles Report</h3>
        <div class="table-wrapper">
            <table class="styled-table" id="vehiclesTable">
                <thead>
                    <tr><th>ID</th><th>Reg Number</th><th>Owner</th><th>Status</th><th>Payment</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php while($v = $vehicles->fetch_assoc()): ?>
                    <tr>
                        <td><?= $v['id']; ?></td>
                        <td><?= htmlspecialchars($v['reg_number']); ?></td>
                        <td><?= htmlspecialchars($v['owner']); ?></td>
                        <td><?= ucfirst($v['status']); ?></td>
                        <td><?= ucfirst($v['payment_status']); ?></td>
                        <td><?= $v['created_at']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Appointments Report -->
    <div id="appointments" class="report-section" style="display:none;">
        <h3>Appointments Report</h3>
        <div class="table-wrapper">
            <table class="styled-table" id="appointmentsTable">
                <thead>
                    <tr><th>ID</th><th>Vehicle</th><th>Date</th><th>Time</th><th>Status</th><th>Created</th></tr>
                </thead>
                <tbody>
                    <?php while($a = $appointments->fetch_assoc()): ?>
                    <tr>
                        <td><?= $a['id']; ?></td>
                        <td><?= $a['reg_number']; ?></td>
                        <td><?= $a['appointment_date']; ?></td>
                        <td><?= $a['appointment_time']; ?></td>
                        <td><?= ucfirst($a['status']); ?></td>
                        <td><?= $a['created_at']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Jobs Report -->
    <div id="jobs" class="report-section" style="display:none;">
        <h3>Jobs Report</h3>
        <div class="table-wrapper">
            <table class="styled-table" id="jobsTable">
                <thead>
                    <tr><th>ID</th><th>Vehicle</th><th>Mechanic</th><th>Status</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php while($j = $jobs->fetch_assoc()): ?>
                    <tr>
                        <td><?= $j['id']; ?></td>
                        <td><?= $j['reg_number']; ?></td>
                        <td><?= htmlspecialchars($j['mechanic']); ?></td>
                        <td><?= ucfirst($j['status']); ?></td>
                        <td><?= $j['created_at']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payments Report -->
    <div id="payments" class="report-section" style="display:none;">
        <h3>Payments Report</h3>
        <div class="table-wrapper">
            <table class="styled-table" id="paymentsTable">
                <thead>
                    <tr><th>ID</th><th>Appointment</th><th>Vehicle</th><th>Owner</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php while($p = $payments->fetch_assoc()): ?>
                    <tr>
                        <td><?= $p['id']; ?></td>
                        <td><?= $p['appointment_id']; ?></td>
                        <td><?= $p['reg_number']; ?></td>
                        <td><?= htmlspecialchars($p['owner']); ?></td>
                        <td><?= number_format($p['amount'],2); ?></td>
                        <td><?= ucfirst($p['method']); ?></td>
                        <td><?= ucfirst($p['status']); ?></td>
                        <td><?= $p['created_at']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Inventory Alerts -->
    <div id="alerts" class="report-section" style="display:none;">
        <h3>Inventory Alerts</h3>
        <div class="table-wrapper">
            <table class="styled-table" id="alertsTable">
                <thead>
                    <tr><th>ID</th><th>Item</th><th>Price</th><th>Instock</th><th>Used</th><th>Remaining</th><th>Updated</th><th>Alert</th></tr>
                </thead>
                <tbody>
                    <?php while($i = $alerts->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i['id']; ?></td>
                        <td><?= htmlspecialchars($i['item_name']); ?></td>
                        <td><?= number_format($i['price'],2); ?></td>
                        <td><?= $i['instock']; ?></td>
                        <td><?= $i['used']; ?></td>
                        <td><?= $i['remaining']; ?></td>
                        <td><?= $i['updated_at']; ?></td>
                        <td>
                            <?php if ($i['remaining'] == 0): ?>
                                <span style="color:red;">❌ Out of Stock</span>
                            <?php else: ?>
                                <span style="color:orange;">⚠️ Low Stock</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ✅ Export Button -->
    <div style="position:fixed; bottom:20px; right:20px;">
    <button onclick="exportReportToCSV()">Export Current Report to CSV</button>
</div>
</main>

<script src="../public/js/global.js"></script>
<script>
function showReport(id) {
    document.querySelectorAll('.report-section').forEach(sec => sec.style.display = 'none');
    document.getElementById(id).style.display = 'block';
    currentReport = id; // track active report
}

// Track active report (default is Vehicles)
let currentReport = "vehicles";

// Unified search functionality
document.getElementById('reportSearch').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let tableId = currentReport + "Table";
    let rows = document.querySelectorAll(`#${tableId} tbody tr`);
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

// ✅ Export to CSV function
function exportReportToCSV() {
    let tableId = currentReport + "Table";
    let table = document.getElementById(tableId);
    let rows = table.querySelectorAll("tr");
    let csv = [];

    rows.forEach(row => {
        let cols = row.querySelectorAll("td, th");
        let rowData = [];
        cols.forEach(col => rowData.push('"' + col.innerText.replace(/"/g, '""') + '"'));
        csv.push(rowData.join(","));
    });

    let csvString = csv.join("\n");
    let blob = new Blob([csvString], { type: "text/csv" });
    let url = window.URL.createObjectURL(blob);

    let a = document.createElement("a");
    a.setAttribute("href", url);
    a.setAttribute("download", currentReport + "_report.csv");
    a.click();
}
</script>