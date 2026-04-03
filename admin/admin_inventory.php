<?php
session_start();
$page_title = "Inventory Management";
$role = "admin";
$dashboard_name = "ADMIN DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
echo '<link rel="stylesheet" href="../admin/admin_styling.css">';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Add new inventory item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    $name  = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    $check = $conn->prepare("SELECT id FROM inventory WHERE item_name = ?");
    $check->bind_param("s", $name);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['popup_message'] = "⚠️ Item name already exists!";
        $_SESSION['popup_type'] = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO inventory (item_name, price, instock, used, updated_at)  
                                VALUES (?, ?, ?, 0, NOW())");
        $stmt->bind_param("sdi", $name, $price, $stock);
        if ($stmt->execute()) {
            $_SESSION['popup_message'] = "✅ Item added successfully!";
            $_SESSION['popup_type'] = "success";
        } else {
            $_SESSION['popup_message'] = "❌ Error adding item!";
            $_SESSION['popup_type'] = "error";
        }
    }
    header("Location: admin_inventory.php"); exit();
}

// Update name and price
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_item'])) {
    $item_id = intval($_POST['item_id']);
    $name    = trim($_POST['name']);
    $price   = floatval($_POST['price']);

    $check = $conn->prepare("SELECT id FROM inventory WHERE item_name = ? AND id != ?");
    $check->bind_param("si", $name, $item_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['popup_message'] = "⚠️ Another item with this name already exists!";
        $_SESSION['popup_type'] = "error";
    } else {
        $stmt = $conn->prepare("UPDATE inventory SET item_name=?, price=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("sdi", $name, $price, $item_id);
        if ($stmt->execute()) {
            $_SESSION['popup_message'] = "✅ Item updated successfully!";
            $_SESSION['popup_type'] = "success";
        } else {
            $_SESSION['popup_message'] = "❌ Error updating item!";
            $_SESSION['popup_type'] = "error";
        }
    }
    header("Location: admin_inventory.php"); exit();
}

// Update instock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_instock'])) {
    $item_id   = intval($_POST['item_id']);
    $new_stock = intval($_POST['new_stock']);

    $stmt = $conn->prepare("UPDATE inventory SET instock = instock + ?, updated_at=NOW() WHERE id=?");
    $stmt->bind_param("ii", $new_stock, $item_id);
    if ($stmt->execute()) {
        $_SESSION['popup_message'] = "✅ Instock updated successfully!";
        $_SESSION['popup_type'] = "success";
    } else {
        $_SESSION['popup_message'] = "❌ Error updating instock!";
        $_SESSION['popup_type'] = "error";
    }
    header("Location: admin_inventory.php"); exit();
}

// Update used
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_used'])) {
    $item_id   = intval($_POST['item_id']);
    $add_used  = intval($_POST['add_used']);

    $stmt = $conn->prepare("SELECT instock, used FROM inventory WHERE id=?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (($row['used'] + $add_used) > $row['instock']) {
        $_SESSION['popup_message'] = "❌ Cannot update: used items exceed instock!";
        $_SESSION['popup_type'] = "error";
    } else {
        $stmt = $conn->prepare("UPDATE inventory SET used = used + ?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("ii", $add_used, $item_id);
        if ($stmt->execute()) {
            $_SESSION['popup_message'] = "✅ Used updated successfully!";
            $_SESSION['popup_type'] = "success";
        } else {
            $_SESSION['popup_message'] = "❌ Error updating used!";
            $_SESSION['popup_type'] = "error";
        }
    }
    header("Location: admin_inventory.php"); exit();
}

// Fetch inventory
$inventory = $conn->query("SELECT id, item_name, price, instock, used, (instock - used) AS remaining, updated_at  
                           FROM inventory ORDER BY updated_at DESC");
?>

<main class="main-content">
    <h2 class="page-heading"><?= $page_title; ?></h2>

    <!-- Popup Messages -->
    <?php include_once("../public/includes/popup.php"); ?>

    <!-- Add Item Form -->
    <form method="POST" class="inline-form">
        <input type="text" name="name" placeholder="Item Name" required>
        <input type="number" step="0.01" name="price" placeholder="Price" required>
        <input type="number" name="stock" placeholder="Initial Stock" required>
        <button type="submit" name="add_item" class="btn-green">Add Item</button>
    </form>

    <!-- Search Box (always stays here) -->
    <input type="text" id="searchBox" placeholder="Search items..." style="margin:15px 0; padding:5px; width:250px;">

    <!-- Update Item Form (appears below search bar when triggered) -->
    <form method="POST" class="inline-form" id="updateForm" style="margin-top:15px; display:none;">
        <input type="hidden" name="item_id" id="update_item_id">
        <input type="text" name="name" id="update_name" placeholder="Item Name" required>
        <input type="number" step="0.01" name="price" id="update_price" placeholder="Price" required>
        <button type="submit" name="update_item" class="btn-blue">Update Name/Price</button>
    </form>

    <!-- Inventory Table -->
    <div class="table-wrapper">
        <div class="table-container">
            <table class="styled-table" id="inventoryTable">
                <thead>
                    <tr>
                        <th>ID</th><th>Name</th><th>Price</th><th>Initial Stock</th>
                        <th>Used</th><th>Remaining</th><th>Updated</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $inventory->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['item_name']); ?></td>
                        <td><?= number_format($row['price'],2); ?></td>
                        <td><?= $row['instock']; ?></td>
                        <td><?= $row['used']; ?></td>
                        <td><?= $row['remaining']; ?></td>
                        <td><?= $row['updated_at']; ?></td>
                        <td>
                            <!-- Select Item for Update -->
                            <button type="button" class="btn-orange" 
                                onclick="selectItem(<?= $row['id']; ?>, '<?= htmlspecialchars($row['item_name']); ?>', <?= $row['price']; ?>)">
                                Edit Name/Price
                            </button>
                            <!-- Update Instock -->
                            <form method="POST" class="inline-form">
                                <input type="hidden" name="item_id" value="<?= $row['id']; ?>">
                                <input type="number" name="new_stock" placeholder="Add Instock" required>
                                <button type="submit" name="update_instock" class="btn-green">Update Instock</button>
                            </form>
                            <!-- Update Used -->
                            <form method="POST" class="inline-form">
                                <input type="hidden" name="item_id" value="<?= $row['id']; ?>">
                                <input type="number" name="add_used" placeholder="Add Used" required>
                                <button type="submit" name="update_used" class="btn-orange">Update Used</button>
                            </form>
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
// Search functionality
document.getElementById('searchBox').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#inventoryTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

// Select item for update
function selectItem(id, name, price) {
    document.getElementById('update_item_id').value = id;
    document.getElementById('update_name').value = name;
    document.getElementById('update_price').value = price;
    document.getElementById('updateForm').style.display = 'block';
    // Scroll to search bar so update form appears right below it
    document.getElementById('searchBox').scrollIntoView({ behavior: 'smooth', block: 'center' });
}
</script>

<?php include_once("../public/includes/footer.php"); ?>