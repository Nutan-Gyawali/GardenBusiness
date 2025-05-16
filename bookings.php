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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings - GreenThumb Garden Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/services.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/bookings.css">
</head>

<body>
    <?php
    require_once 'config.php';
    require_once 'booking_functions.php';

    $customers = $pdo->query("SELECT customer_id, customer_name FROM customers")->fetchAll();
    $services = $pdo->query("SELECT service_id, service_name FROM services")->fetchAll();
    $bookings = getAllBookings();

    $editBooking = null;
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $editBooking = getBookingById($_GET['edit']);
    }
    ?>

    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-leaf"></i>
            <span>GreenThumb</span>
        </div>
        <nav>
            <ul>
            <li><a href="services.php"><i class="fas fa-tools"></i> Services</a></li>
                <li><a href="bookings.php" class="active"><i class="fas fa-calendar-alt"></i> Bookings</a></li>
                <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>

            </ul>
        </nav>
    </div>

    <div class="main-content">
        <header>
            <div class="header-content">
                <button id="sidebar-toggle"><i class="fas fa-bars"></i></button>
                <h1>Bookings</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span>Admin</span>
                </div>
            </div>
        </header>

        <div class="content-container">
            <div class="page-header">
                <h2>Manage Bookings</h2>
                <button class="btn btn-primary" id="show-add-form">
                    <i class="fas fa-plus"></i> Add Booking
                </button>
            </div>

            <!-- Booking Form -->
            <div class="form-container" id="booking-form" style="display: <?php echo $editBooking ? 'block' : 'none'; ?>">
                <div class="card">
                    <div class="card-header">
                        <h3><?php echo $editBooking ? 'Edit Booking' : 'Add New Booking'; ?></h3>
                        <button class="close-btn" id="close-form"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="card-body">
                        <form id="bookingForm" method="POST" action="booking_functions.php">
                            <input type="hidden" name="action" value="<?php echo $editBooking ? 'update' : 'add'; ?>">
                            <?php if ($editBooking): ?>
                                <input type="hidden" name="booking_id" value="<?= $editBooking['booking_id'] ?>">
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="customer_id">Customer <span class="required">*</span></label>
                                <select id="customer_id" name="customer_id" class="form-control" required>
                                    <option value="">-- Select Customer --</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?= $customer['customer_id'] ?>" <?= $editBooking && $editBooking['customer_id'] == $customer['customer_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($customer['customer_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="error-message" id="customer-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="service_id">Service <span class="required">*</span></label>
                                <select id="service_id" name="service_id" class="form-control" required>
                                    <option value="">-- Select Service --</option>
                                    <?php foreach ($services as $service): ?>
                                        <option value="<?= $service['service_id'] ?>" <?= $editBooking && $editBooking['service_id'] == $service['service_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($service['service_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="error-message" id="service-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="booking_date">Booking Date <span class="required">*</span></label>
                                <input type="date" id="booking_date" name="booking_date" class="form-control" value="<?= $editBooking['booking_date'] ?? '' ?>" required>
                                <span class="error-message" id="date-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="time_worked">Time Worked (hours) <span class="required">*</span></label>
                                <input type="number" id="time_worked" name="time_worked" class="form-control" step="0.1" min="0" value="<?= $editBooking['time_worked'] ?? '' ?>" required>
                                <span class="error-message" id="time-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="status">Status <span class="required">*</span></label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="pending" <?= $editBooking && $editBooking['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="completed" <?= $editBooking && $editBooking['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea id="notes" name="notes" class="form-control" rows="3"><?= $editBooking['notes'] ?? '' ?></textarea>
                            </div>

                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary" id="cancel-form">Cancel</button>
                                <button type="submit" class="btn btn-primary"><?= $editBooking ? 'Update Booking' : 'Add Booking' ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Bookings Table -->
            <div class="table-container">
                <div class="cards">
                    <div class="card-body">
                        <table class="data-table">
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
                                <?php if (empty($bookings)): ?>
                                    <tr>
                                        <td colspan="7" class="no-data">No bookings found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($booking['customer_name']) ?></td>
                                            <td><?= htmlspecialchars($booking['service_name']) ?></td>
                                            <td><?= date('M d, Y', strtotime($booking['booking_date'])) ?></td>
                                            <td><?= htmlspecialchars($booking['time_worked']) ?></td>
                                            <td>
                                                <span class="status-badge <?= strtolower($booking['status']) ?>">
                                                    <?= ucfirst(htmlspecialchars($booking['status'])) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($booking['notes'] ?? '') ?></td>
                                            <td class="actions">
                                                <a href="?edit=<?= $booking['booking_id'] ?>" class="btn-icon edit" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="booking_functions.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this booking?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                                    <button type="submit" class="btn-icon delete" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Get form elements
            const bookingForm = document.getElementById("booking-form");
            const closeFormBtn = document.getElementById("close-form");
            const cancelFormBtn = document.getElementById("cancel-form");
            const showAddFormBtn = document.getElementById("show-add-form");

            // Show Add Form button handling
            if (showAddFormBtn) {
                showAddFormBtn.addEventListener("click", function(e) {
                    e.preventDefault();
                    bookingForm.style.display = "block";
                });
            }

            // Close form button handling
            if (closeFormBtn) {
                closeFormBtn.addEventListener("click", function() {
                    window.location.href = "bookings.php";
                });
            }

            // Cancel form button handling
            if (cancelFormBtn) {
                cancelFormBtn.addEventListener("click", function() {
                    window.location.href = "bookings.php";
                });
            }

            // Form validation
            const form = document.getElementById("bookingForm");
            if (form) {
                form.addEventListener("submit", function(event) {
                    let isValid = true;

                    // Validate customer selection
                    const customerSelect = document.getElementById("customer_id");
                    const customerError = document.getElementById("customer-error");
                    if (!customerSelect.value) {
                        customerError.textContent = "Please select a customer";
                        isValid = false;
                    } else {
                        customerError.textContent = "";
                    }

                    // Validate service selection
                    const serviceSelect = document.getElementById("service_id");
                    const serviceError = document.getElementById("service-error");
                    if (!serviceSelect.value) {
                        serviceError.textContent = "Please select a service";
                        isValid = false;
                    } else {
                        serviceError.textContent = "";
                    }

                    // Validate booking date
                    const dateInput = document.getElementById("booking_date");
                    const dateError = document.getElementById("date-error");
                    if (!dateInput.value) {
                        dateError.textContent = "Please select a date";
                        isValid = false;
                    } else {
                        dateError.textContent = "";
                    }

                    // Validate time worked
                    const timeInput = document.getElementById("time_worked");
                    const timeError = document.getElementById("time-error");
                    if (!timeInput.value || isNaN(parseFloat(timeInput.value)) || parseFloat(timeInput.value) < 0) {
                        timeError.textContent = "Please enter valid hours worked";
                        isValid = false;
                    } else {
                        timeError.textContent = "";
                    }

                    if (!isValid) {
                        event.preventDefault();
                    }
                });
            }

            // Sidebar toggle
            const sidebarToggle = document.getElementById("sidebar-toggle");
            const sidebar = document.querySelector(".sidebar");
            const mainContent = document.querySelector(".main-content");

            if (sidebarToggle && sidebar && mainContent) {
                sidebarToggle.addEventListener("click", function() {
                    sidebar.classList.toggle("collapsed");
                    mainContent.classList.toggle("expanded");
                });
            }
        });
    </script>
</body>

</html>