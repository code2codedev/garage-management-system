<?php
session_start();
$page_title = "Admin Dashboard";
$role = "admin";
$dashboard_name = "ADMIN DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
echo '<link rel="stylesheet" href="../admin/admin_styling.css">';
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");

// Totals
$totalVehicles     = $conn->query("SELECT COUNT(*) AS total FROM vehicles")->fetch_assoc()['total'];
$totalAppointments = $conn->query("SELECT COUNT(*) AS total FROM appointments")->fetch_assoc()['total'];
$totalJobs         = $conn->query("SELECT COUNT(*) AS total FROM jobs")->fetch_assoc()['total'];
$totalPayments     = $conn->query("SELECT COUNT(*) AS total FROM payments")->fetch_assoc()['total'];
$totalMessages     = $conn->query("SELECT COUNT(*) AS total FROM messages")->fetch_assoc()['total'];

// Payment methods breakdown
$paymentMethods = $conn->query("SELECT method, COUNT(*) AS count FROM payments GROUP BY method");

// Job statuses breakdown
$jobStatuses = $conn->query("SELECT status, COUNT(*) AS count FROM jobs GROUP BY status");

// Appointments per day (last 7 days)
$appointmentsTrend = $conn->query("SELECT DATE(appointment_date) AS day, COUNT(*) AS count 
                                   FROM appointments 
                                   WHERE appointment_date >= CURDATE() - INTERVAL 7 DAY 
                                   GROUP BY day ORDER BY day ASC");

// Inventory breakdown
$inventoryData = $conn->query("SELECT item_name, remaining FROM inventory ORDER BY remaining ASC");
?>

<main class="main-content">
    <h2 class="page-heading"><?= $page_title; ?></h2>

    <!-- Flex wrapper -->
    <div class="dashboard-flex">
        <!-- Left side: Totals -->
        <div class="totals-box chart-container">
            <h3>Totals</h3>
            <canvas id="totalsChart"></canvas>
        </div>

        <!-- Right side: stacked column -->
        <div class="right-box">
            <div class="chart-container">
                <h3>Payment Methods</h3>
                <canvas id="paymentChart"></canvas>
            </div>
            <div class="chart-container">
                <h3>Job Statuses</h3>
                <canvas id="jobChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Appointments Line Chart -->
    <div class="chart-container" style="max-width:800px; margin-top:30px;">
        <h3>Appointments (Last 7 Days)</h3>
        <canvas id="appointmentsChart"></canvas>
    </div>

    <!-- Inventory Bar Chart -->
    <div class="chart-container" style="max-width:800px; margin-top:30px;">
        <h3>Inventory Levels</h3>
        <canvas id="inventoryChart"></canvas>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Totals Bar Chart
new Chart(document.getElementById('totalsChart'), {
    type: 'bar',
    data: {
        labels: ['Vehicles','Appointments','Jobs','Payments','Messages'],
        datasets: [{
            label: 'Totals',
            data: [<?= $totalVehicles; ?>, <?= $totalAppointments; ?>, <?= $totalJobs; ?>, <?= $totalPayments; ?>, <?= $totalMessages; ?>],
            backgroundColor: ['#007bff','#28a745','#ffc107','#17a2b8','#6f42c1']
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

// Payment Methods Pie Chart
new Chart(document.getElementById('paymentChart'), {
    type: 'pie',
    data: {
        labels: [<?php while($pm = $paymentMethods->fetch_assoc()){ echo "'".ucfirst($pm['method'])."',"; } ?>],
        datasets: [{
            data: [<?php $paymentMethods->data_seek(0); while($pm = $paymentMethods->fetch_assoc()){ echo $pm['count'].","; } ?>],
            backgroundColor: ['#007bff','#28a745','#ffc107','#17a2b8','#6f42c1']
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

// Job Status Doughnut Chart
new Chart(document.getElementById('jobChart'), {
    type: 'doughnut',
    data: {
        labels: [<?php while($js = $jobStatuses->fetch_assoc()){ echo "'".ucfirst($js['status'])."',"; } ?>],
        datasets: [{
            data: [<?php $jobStatuses->data_seek(0); while($js = $jobStatuses->fetch_assoc()){ echo $js['count'].","; } ?>],
            backgroundColor: ['#28a745','#ffc107','#dc3545','#17a2b8']
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

// Appointments Line Chart
new Chart(document.getElementById('appointmentsChart'), {
    type: 'line',
    data: {
        labels: [<?php while($at = $appointmentsTrend->fetch_assoc()){ echo "'".$at['day']."',"; } ?>],
        datasets: [{
            label: 'Appointments',
            data: [<?php $appointmentsTrend->data_seek(0); while($at = $appointmentsTrend->fetch_assoc()){ echo $at['count'].","; } ?>],
            borderColor: '#007bff',
            fill: false
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

// Inventory Bar Chart
new Chart(document.getElementById('inventoryChart'), {
    type: 'bar',
    data: {
        labels: [<?php while($inv = $inventoryData->fetch_assoc()){ echo "'".htmlspecialchars($inv['item_name'])."',"; } ?>],
        datasets: [{
            label: 'Remaining Stock',
            data: [<?php $inventoryData->data_seek(0); while($inv = $inventoryData->fetch_assoc()){ echo $inv['remaining'].","; } ?>],
            backgroundColor: '#ffc107'
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});
</script>

<?php include_once("../public/includes/footer.php"); ?>