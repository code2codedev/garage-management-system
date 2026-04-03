<?php
session_start();
$page_title = "Payments";
$role = "receptionist";
$dashboard_name = "RECEPTIONIST DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../receptionist/receptionist_styling.css">';

// Record payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_payment'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $amount         = floatval($_POST['amount']);
    $method         = trim($_POST['method']);

    $stmt = $conn->prepare("INSERT INTO payments (appointment_id, amount, method, status, created_at) 
                            VALUES (?, ?, ?, 'paid', NOW())");
    $stmt->bind_param("ids", $appointment_id, $amount, $method);

    if ($stmt->execute()) {
        $conn->query("UPDATE appointments SET status='paid' WHERE id=$appointment_id");
        $_SESSION['popup_message'] = "✅ Payment recorded!";
        $_SESSION['popup_type'] = "success";
    } else {
        $_SESSION['popup_message'] = "❌ Error recording payment!";
        $_SESSION['popup_type'] = "error";
    }
    header("Location: receptionist_payments.php"); exit();
}

// Fetch appointments needing payment
$appointments = $conn->query("SELECT a.id, v.reg_number, u.username AS owner, v.phone
                              FROM appointments a
                              JOIN vehicles v ON a.vehicle_id=v.id
                              JOIN users u ON v.owner_id=u.id
                              LEFT JOIN payments p ON a.id = p.appointment_id
                              WHERE (a.status='in_progress' OR a.status='completed')
                                AND (p.id IS NULL OR p.status='unpaid')
                              ORDER BY a.created_at DESC");

// Fetch payments (recent 10)
$payments = $conn->query("SELECT p.id, a.id AS appointment_id, v.reg_number, u.username AS owner, 
                                 p.amount, p.method, p.status, p.created_at
                          FROM payments p
                          JOIN appointments a ON p.appointment_id=a.id
                          JOIN vehicles v ON a.vehicle_id=v.id
                          JOIN users u ON v.owner_id=u.id
                          ORDER BY p.created_at DESC LIMIT 10");
?>

<main class="main-content">
    <h2 class="page-heading"><?= $page_title; ?></h2>
    <?php include_once("../public/includes/popup.php"); ?>

    <!-- Record Payment Form -->
    <form method="POST" class="inline-form" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <input list="appointmentList" id="appointmentId" name="appointment_id" placeholder="Search Appointment (Vehicle + Owner)" required>
        <datalist id="appointmentList">
            <?php while($a = $appointments->fetch_assoc()): ?>
                <option value="<?= $a['id']; ?>"  
                        data-reg="<?= $a['reg_number']; ?>"  
                        data-owner="<?= htmlspecialchars($a['owner']); ?>"  
                        data-phone="<?= $a['phone']; ?>">
                    <?= $a['reg_number']; ?> (Owner: <?= htmlspecialchars($a['owner']); ?>)
                </option>
            <?php endwhile; ?>
        </datalist>

        <input type="number" step="0.01" id="amountField" name="amount" placeholder="Amount" required>
        <select name="method" required>
            <option value="cash">Cash</option>
            <option value="mpesa">M-Pesa</option>
            <option value="card">Card</option>
        </select>
        <button type="submit" name="record_payment" class="btn-green">Record Payment</button>

        <!-- Inline Search beside Record Payment button -->
        <input type="text" id="searchPayments" placeholder="Search payments...">
    </form>

    <!-- Send Invoice Form (WhatsApp intact) -->
    <form class="inline-form" style="margin-top:20px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <input list="invoiceAppointmentList" id="invoiceAppointmentId" placeholder="Search Appointment (Vehicle + Owner)" required>
        <datalist id="invoiceAppointmentList">
            <?php
            $appointments2 = $conn->query("SELECT a.id, v.reg_number, u.username AS owner, v.phone
                                           FROM appointments a
                                           JOIN vehicles v ON a.vehicle_id=v.id
                                           JOIN users u ON v.owner_id=u.id
                                           LEFT JOIN payments p ON a.id = p.appointment_id
                                           WHERE (a.status='in_progress' OR a.status='completed')
                                             AND (p.id IS NULL OR p.status='unpaid')
                                           ORDER BY a.created_at DESC");
            while($a = $appointments2->fetch_assoc()): ?>
                <option value="<?= $a['id']; ?>"  
                        data-reg="<?= $a['reg_number']; ?>"  
                        data-owner="<?= htmlspecialchars($a['owner']); ?>"  
                        data-phone="<?= $a['phone']; ?>">
                    <?= $a['reg_number']; ?> (Owner: <?= htmlspecialchars($a['owner']); ?>)
                </option>
            <?php endwhile; ?>
        </datalist>
        <button type="button" class="btn-blue" onclick="sendInvoice()">Send Invoice via WhatsApp</button>
    </form>

    <!-- Payments Table -->
    <div class="table-wrapper">
        <div class="table-container">
            <table class="styled-table" id="paymentsTable">
                <thead><tr><th>ID</th><th>Appointment</th><th>Vehicle</th><th>Owner</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    <?php while($row = $payments->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['appointment_id']; ?></td>
                        <td><?= $row['reg_number']; ?></td>
                        <td><?= htmlspecialchars($row['owner']); ?></td>
                        <td><?= number_format($row['amount'],2); ?></td>
                        <td><?= ucfirst($row['method']); ?></td>
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
// Auto-fill amount when appointment is selected
document.getElementById("appointmentId").addEventListener("input", function() {
    var appointmentId = this.value;
    if (!appointmentId) return;

    fetch("fetch_services.php?appointment_id=" + appointmentId)
        .then(response => response.json())
        .then(data => {
            var total = 0;
            data.forEach(function(s) {
                total += parseFloat(s.price);
            });

            var serviceFee = 300;
            total += serviceFee;

            document.getElementById("amountField").value = total.toFixed(2);
        })
        .catch(err => console.error("Error fetching services:", err));
});

// WhatsApp invoice function (unchanged)
function sendInvoice() {
    var appointmentId = document.getElementById("invoiceAppointmentId").value;
    if (!appointmentId) {
        alert("Please select an appointment.");
        return;
    }

    var options = document.querySelectorAll("#invoiceAppointmentList option");
    var reg = "", owner = "", phone = "";
    options.forEach(function(opt) {
        if (opt.value === appointmentId) {
            reg = opt.getAttribute("data-reg");
            owner = opt.getAttribute("data-owner");
            phone = opt.getAttribute("data-phone");
        }
    });

    if (!phone) {
        alert("No phone number found for this appointment.");
        return;
    }

    if (phone.startsWith("07") || phone.startsWith("01")) {
        phone = "254" + phone.substring(1);
    }

    fetch("fetch_services.php?appointment_id=" + appointmentId)
        .then(response => response.json())
        .then(data => {
            var message = "📋 *Alpha Garage Invoice*\n\n";
            message += "Vehicle Reg: " + reg + "\n";
            message += "Owner: " + owner + "\n";
            message += "Phone: " + phone + "\n\n";

            var total = 0;
            message += "Services:\n";
            data.forEach(function(s) {
                message += "- " + s.name + " (" + s.price + ")\n";
                total += parseFloat(s.price);
            });

           // Add fixed service fee
            var serviceFee = 300;
            message += "- Admin Fee (" + serviceFee + ")\n";
            total += serviceFee;

            message += "\nTotal: " + total.toFixed(2) + "\n\n";


            message += "💳 *Payment Methods*\n";
            message += "- M-Pesa Paybill: 0000 (Account: 1111)\n";
            message += "- M-Pesa Number: 88888888\n";
            message += "- Bank Account: 44444444\n\n";

            message += "Thank you for choosing our garage! 🚗🔧";

            var url = "https://wa.me/" + phone + "?text=" + "?text=" + encodeURIComponent(message);window.open(url, "_blank");
            }
        )
        ;
    }

</script>

<style>.table-wrapper max-height: 220px;overflow-y: </style>
<?php include_once("../public/includes/footer.php"); ?>
