<?php
session_start();
require_once 'service_functions.php';

// CSRF Token Setup
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Service - GreenThumb</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="form-container">
        <div class="card">
            <div class="card-header">
                <h3>Add New Service</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="service_functions.php" id="addServiceForm">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="form-group">
                        <label for="service_name">Service Name <span class="required">*</span></label>
                        <input type="text" id="service_name" name="service_name" class="form-control" required>
                        <span class="error-message" id="name-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="price_per_hour">Price per Hour ($) <span class="required">*</span></label>
                        <input type="number" id="price_per_hour" name="price_per_hour" class="form-control" step="0.01" min="0" required>
                        <span class="error-message" id="price-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="services.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Add Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Optional Client-Side Validation -->
    <script>
        document.getElementById("addServiceForm").addEventListener("submit", function(e) {
            let isValid = true;

            const name = document.getElementById("service_name").value.trim();
            const price = document.getElementById("price_per_hour").value.trim();

            if (!name) {
                document.getElementById("name-error").textContent = "Service name is required";
                isValid = false;
            } else {
                document.getElementById("name-error").textContent = "";
            }

            if (!price || isNaN(price) || parseFloat(price) < 0) {
                document.getElementById("price-error").textContent = "Please enter a valid price";
                isValid = false;
            } else {
                document.getElementById("price-error").textContent = "";
            }

            if (!isValid) e.preventDefault();
        });
    </script>
</body>

</html>