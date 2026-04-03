<?php
session_start();
$page_title = "Inventory Usage";
$role = "mechanic";
$dashboard_name = "MECHANIC DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../mechanic/mechanic_styling.css">';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Update inventory usage
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_inventory'])) {
    $vehicle_id = intval($_POST['vehicle_id']);
    $valid = true;

    foreach ($_POST['items'] as $item_id => $data) {
        $used_qty = intval($data['qty']);
        $selected = isset($data['selected']) ? 1 : 0;

        if ($selected && $used_qty <= 0) {
            $valid = false;
            $_SESSION['popup_message'] = "❌ Selected item #$item_id has no quantity entered!";
            $_SESSION['popup_type'] = "error";
            break;
        }
        if (!$selected && $used_qty > 0) {
            $valid = false;
            $_SESSION['popup_message'] = "❌ Item #$item_id has quantity entered but not selected!";
            $_SESSION['popup_type'] = "error";
            break;
        }
    }

    if ($valid) {
        foreach ($_POST['items'] as $item_id => $data) {
            $used_qty = intval($data['qty']);
            $selected = isset($data['selected']) ? 1 : 0;

            if ($selected && $used_qty > 0) {
                $stmt = $conn->prepare("UPDATE inventory SET used=used+?, remaining=instock-used, updated_at=NOW() WHERE id=? AND remaining>=?");
                $stmt->bind_param("iii", $used_qty, $item_id, $used_qty);
                if ($stmt->execute()) {
                    $action = "Used $used_qty of item #$item_id for vehicle #$vehicle_id";
                    $log = $conn->prepare("INSERT INTO history (action, vehicle_id, performed_by_id, created_at) VALUES (?, ?, ?, NOW())");
                    $log->bind_param("sii", $action, $vehicle_id, $_SESSION['user_id']);
                    $log->execute();
                }
            }
        }
        $_SESSION['popup_message'] = "✅ Inventory updated!";
        $_SESSION['popup_type'] = "success";
    }
    header("Location: mechanic_inventory.php"); exit();
}

// Fetch vehicles assigned to mechanic
$vehicles = $conn->query("SELECT DISTINCT v.id, v.reg_number, u.username AS owner
                          FROM jobs j
                          JOIN vehicles v ON j.vehicle_id=v.id
                          JOIN users u ON v.owner_id=u.id
                          WHERE j.mechanic_id=".$_SESSION['user_id']." AND j.status='in_progress'");

// Fetch inventory
$inventory = $conn->query("SELECT id, item_name, price, instock, used, remaining FROM inventory ORDER BY item_name ASC");
?>

<main class="main-content">
    <div>
        <h2 class="page-heading"><?= $page_title; ?></h2>
    </div>

    <?php include_once("../public/includes/popup.php"); ?>

    <form method="POST" id="inventoryForm">
        <!-- Vehicle Selection -->
        <label>Select Vehicle:</label>
        <select name="vehicle_id" id="vehicleSelect" required onchange="showSelectedVehicle(this)">
            <option value="">-- Select Vehicle --</option>
            <?php while($v = $vehicles->fetch_assoc()): ?>
                <option value="<?= $v['id']; ?>" data-reg="<?= htmlspecialchars($v['reg_number']); ?>" data-owner="<?= htmlspecialchars($v['owner']); ?>">
                    <?= $v['reg_number']; ?> (Owner: <?= htmlspecialchars($v['owner']); ?>)
                </option>
            <?php endwhile; ?>
            <!-- Inline Search beside heading -->
        <input type="text" id="searchInventory" placeholder="Search inventory...">
        </select>

        <!-- Inventory Table -->
        <div class="table-wrapper">
            <div class="table-container">
                <table class="styled-table" id="inventoryTable">
                    <thead>
                        <tr><th>Select</th><th>ID</th><th>Name</th><th>Price</th><th>In Stock</th><th>Used</th><th>Remaining</th><th>Use Qty</th></tr>
                    </thead>
                    <tbody>
                        <?php while($row = $inventory->fetch_assoc()): ?>
                        <tr>
                            <td><input type="checkbox" name="items[<?= $row['id']; ?>][selected]" onchange="updateSummary()"></td>
                            <td><?= $row['id']; ?></td>
                            <td><?= htmlspecialchars($row['item_name']); ?></td>
                            <td><?= number_format($row['price'],2); ?></td>
                            <td><?= $row['instock']; ?></td>
                            <td><?= $row['used']; ?></td>
                            <td><?= $row['remaining']; ?></td>
                            <td><input type="number" name="items[<?= $row['id']; ?>][qty]" min="0" max="<?= $row['remaining']; ?>" onchange="updateSummary()"></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Vehicle Summary + Selected Items + Update Button -->
        <div id="summaryBox" style="margin:15px 0; display:flex; align-items:center; justify-content:space-between; border:1px solid #ccc; padding:10px;">
            <div>
                <strong>Vehicle:</strong> <span id="summaryVehicle"></span> |
                <strong>Owner:</strong> <span id="summaryOwner"></span><br>
                <strong>Selected Items:</strong> <span id="summaryItems"></span>
            </div>
            <button type="submit" name="update_inventory" class="btn-green">Update Inventory</button>
        </div>
    </form>
</main>

<script src="../public/js/global.js"></script>
<script>
function showSelectedVehicle(select) {
    var option = select.options[select.selectedIndex];
    if(option.value) {
        document.getElementById('summaryBox').style.display = 'flex';
        document.getElementById('summaryVehicle').textContent = option.getAttribute('data-reg');
        document.getElementById('summaryOwner').textContent = option.getAttribute('data-owner');
    } else {
        document.getElementById('summaryBox').style.display = 'none';
    }
    updateSummary();
}

function updateSummary() {
    var summarySpan = document.getElementById('summaryItems');
    summarySpan.textContent = ''; 
    var rows = document.querySelectorAll('table.styled-table tbody tr');
    var items = [];
    rows.forEach(function(row) {
        var checkbox = row.querySelector('input[type="checkbox"]');
        var qtyInput = row.querySelector('input[type="number"]');
        if(checkbox.checked && qtyInput.value > 0) {
            var itemName = row.cells[2].textContent;
            var qty = qtyInput.value;
            items.push(itemName + " (Qty: " + qty + ")");
        }
    });
    summarySpan.textContent = items.join(" | ");
}

// Search functionality for Inventory
document.getElementById('searchInventory').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#inventoryTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<?php include_once("../public/includes/footer.php"); ?>