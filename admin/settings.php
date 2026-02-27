<?php
$page_title = 'Settings';
include_once 'includes/header.php';

// Fetch settings
$settings_res = $conn->query("SELECT * FROM settings");
$settings = [];
while($row = $settings_res->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach($_POST as $key => $value) {
        $key = sanitize($key);
        $value = sanitize($value);
        $conn->query("UPDATE settings SET setting_value = '$value' WHERE setting_key = '$key'");
    }
    header("Location: settings.php?msg=updated");
    exit;
}
?>

<div class="animate__animated animate__fadeIn">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h1 style="font-size: 2.5rem;">System Settings</h1>
            <p style="color: #64748b;">Configure your store parameters and business details.</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem;">
        <div class="admin-card">
            <h3 style="margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem;"><i class="fas fa-truck text-primary"></i> Logistics & Delivery</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Delivery Charge (₹)</label>
                    <input type="number" name="delivery_charge" class="form-control" value="<?php echo $settings['delivery_charge'] ?? 40; ?>">
                    <small style="color: #64748b; margin-top: 0.5rem; display: block;">Flat rate applied to every order.</small>
                </div>
                <div class="form-group" style="margin-top: 1.5rem;">
                    <label>Store Status</label>
                    <select name="store_status" class="form-control">
                        <option value="open" <?php echo ($settings['store_status'] ?? 'open') == 'open' ? 'selected' : ''; ?>>Open for Business</option>
                        <option value="closed" <?php echo ($settings['store_status'] ?? 'open') == 'closed' ? 'selected' : ''; ?>>Closed / Maintenance</option>
                    </select>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1.5rem;">
                    <div class="form-group">
                        <label>Opening Time</label>
                        <input type="time" name="store_opening_time" class="form-control" value="<?php echo $settings['store_opening_time'] ?? '08:00'; ?>">
                    </div>
                    <div class="form-group">
                        <label>Closing Time</label>
                        <input type="time" name="store_closing_time" class="form-control" value="<?php echo $settings['store_closing_time'] ?? '22:00'; ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 2rem;">Save Logistics</button>
            </form>
        </div>

        <div class="admin-card">
            <h3 style="margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem;"><i class="fas fa-store text-primary"></i> Contact Information</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Support Email</label>
                    <input type="email" name="contact_email" class="form-control" value="<?php echo $settings['contact_email'] ?? 'support@grocart.com'; ?>">
                </div>
                <div class="form-group" style="margin-top: 1.5rem;">
                    <label>Support Phone</label>
                    <input type="text" name="contact_phone" class="form-control" value="<?php echo $settings['contact_phone'] ?? '+91-1234567890'; ?>">
                </div>
                <hr style="margin: 2rem 0; border: none; border-top: 2px solid #f1f5f9;">
                <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 2rem;">These details are displayed in the footer and order receipts.</p>
                <button type="submit" class="btn btn-primary btn-block">Save Contact Info</button>
            </form>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
