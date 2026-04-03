<aside class="sidebar">
    <ul>
        <?php if ($role === "admin"): ?>
            <li><a href="/GMS_TEST/admin/admin_dashboard.php">Dashboard</a></li>
            <li><a href="/GMS_TEST/admin/admin_users.php">Manage Users</a></li>
            <li><a href="/GMS_TEST/admin/admin_jobs.php">Jobs</a></li>
            <li><a href="/GMS_TEST/admin/admin_services.php">Services</a></li>
            <li><a href="/GMS_TEST/admin/admin_inventory.php">Inventory</a></li>
            <li><a href="/GMS_TEST/admin/admin_reports.php">Reports</a></li>
            <li><a href="/GMS_TEST/admin/admin_history.php">History</a></li>
        <?php elseif ($role === "receptionist"): ?>
            <li><a href="/GMS_TEST/receptionist/receptionist_dashboard.php">Dashboard</a></li>
            <li><a href="/GMS_TEST/receptionist/receptionist_vehicle_registration.php">Vehicles</a></li>
            <li><a href="/GMS_TEST/receptionist/receptionist_appointments.php">Appointments</a></li>
            <li><a href="/GMS_TEST/receptionist/receptionist_payments.php">Payments</a></li>
            <li><a href="/GMS_TEST/receptionist/receptionist_messages.php">Messages</a></li>
        <?php elseif ($role === "mechanic"): ?>
            <li><a href="/GMS_TEST/mechanic/mechanic_dashboard.php">Dashboard</a></li>
            <li><a href="/GMS_TEST/mechanic/mechanic_jobs.php">Jobs</a></li>
            <li><a href="/GMS_TEST/mechanic/mechanic_inventory.php">Inventory</a></li>
            <li><a href="/GMS_TEST/mechanic/mechanic_history.php">History</a></li>
        <?php elseif ($role === "customer"): ?>
            <li><a href="/GMS_TEST/customer/customer_dashboard.php">Dashboard</a></li>
            <li><a href="/GMS_TEST/customer/customer_vehicles.php">Vehicles</a></li>
            <li><a href="/GMS_TEST/customer/customer_appointments.php">Appointments</a></li>
            <li><a href="/GMS_TEST/customer/customer_payments.php">Payments</a></li>
            <li><a href="/GMS_TEST/customer/customer_messages.php">Messages</a></li>
            <li><a href="/GMS_TEST/customer/customer_history.php">History</a></li>
            
        <?php endif; ?>
    </ul>
</aside>