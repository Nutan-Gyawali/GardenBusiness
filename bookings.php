<?php
require_once 'config.php'; // contains $pdo
require_once 'booking_functions.php'; // your code above

$customers = $pdo->query("SELECT customer_id, customer_name FROM customers")->fetchAll();
$services = $pdo->query("SELECT service_id, service_name FROM services")->fetchAll();
$bookings = getAllBookings();

$editBooking = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editBooking = getBookingById($_GET['edit']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bookings - GreenThumb</title>
    <link rel="stylesheet" href="css/services.css">
    <style> </style>
</head>

<body>
    <div class="container">
        <h2><?php echo $editBooking ? 'Edit Booking' : 'Add New Booking'; ?></h2>

        <form method="POST" id="bookingForm">
            <input type="hidden" name="action" value="<?php echo $editBooking ? 'update' : 'add'; ?>">
            <?php if ($editBooking): ?>
                <input type="hidden" name="booking_id" value="<?= $editBooking['booking_id'] ?>">
            <?php endif; ?>

            <label>Customer</label>
            <select name="customer_id" required>
                <option value="">-- Select --</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?= $customer['customer_id'] ?>" <?= $editBooking && $editBooking['customer_id'] == $customer['customer_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($customer['customer_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Service</label>
            <select name="service_id" required>
                <option value="">-- Select --</option>
                <?php foreach ($services as $service): ?>
                    <option value="<?= $service['service_id'] ?>" <?= $editBooking && $editBooking['service_id'] == $service['service_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($service['service_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Booking Date</label>
            <input type="date" name="booking_date" value="<?= $editBooking['booking_date'] ?? '' ?>" required>

            <label>Time Worked (hours)</label>
            <input type="number" step="0.1" name="time_worked" value="<?= $editBooking['time_worked'] ?? '' ?>" required>

            <label>Status</label>
            <select name="status">
                <option value="pending" <?= $editBooking && $editBooking['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="completed" <?= $editBooking && $editBooking['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
            </select>

            <label>Notes</label>
            <textarea name="notes" rows="3"><?= $editBooking['notes'] ?? '' ?></textarea>

            <div class="form-actions">
                <a href="bookings.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><?= $editBooking ? 'Update' : 'Book' ?></button>
            </div>
        </form>

        <hr>

        <h3>All Bookings</h3>
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Hours</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['customer_name']) ?></td>
                        <td><?= htmlspecialchars($booking['service_name']) ?></td>
                        <td><?= htmlspecialchars($booking['booking_date']) ?></td>
                        <td><?= htmlspecialchars($booking['time_worked']) ?></td>
                        <td><?= htmlspecialchars($booking['status']) ?></td>
                        <td><?= htmlspecialchars($booking['notes']) ?></td>
                        <td>
                            <a href="?edit=<?= $booking['booking_id'] ?>">Edit</a>
                            <form method="POST" action="booking_functions.php" style="display:inline;" onsubmit="return confirm('Delete this booking?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.getElementById("bookingForm").addEventListener("submit", function(e) {
            const time = parseFloat(document.querySelector('[name="time_worked"]').value);
            if (isNaN(time) || time <= 0) {
                alert("Time worked must be a positive number");
                e.preventDefault();
            }
        });
    </script>
</body>

</html>