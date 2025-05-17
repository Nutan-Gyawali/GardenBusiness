<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Customer - GreenThumb</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/bookings.css">
    <link rel="stylesheet" href="css/common.css">

</head>

<body>
    <?php
    // Generate CSRF token
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    ?>

    <div class="form-container">
        <div class="card">
            <div class="card-header">
                <h3>Add New Customer</h3>
            </div>
            <div class="card-body">
                <form id="addCustomerForm" method="POST" action="customer_functions.php">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="form-group">
                        <label for="customer_name">Customer Name <span class="required">*</span></label>
                        <input type="text" id="customer_name" name="customer_name" class="form-control" required>
                        <span class="error-message" id="name-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="address">Address <span class="required">*</span></label>
                        <input type="text" id="address" name="address" class="form-control" required>
                        <span class="error-message" id="address-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" class="form-control" required>
                        <span class="error-message" id="phone-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="email">Email (optional)</label>
                        <input type="email" id="email" name="email" class="form-control">
                        <span class="error-message" id="email-error"></span>
                    </div>

                    <div class="form-actions">
                        <a href="customers.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Add Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("addCustomerForm").addEventListener("submit", function(event) {
            let isValid = true;

            // Get form values and trim whitespace
            const name = document.getElementById("customer_name").value.trim();
            const phone = document.getElementById("phone").value.trim();
            const email = document.getElementById("email").value.trim();

            // Clear previous error messages
            document.getElementById("name-error").textContent = "";
            document.getElementById("phone-error").textContent = "";
            document.getElementById("email-error").textContent = "";

            // Validate name (must be at least 3 characters)
            if (name === "") {
                document.getElementById("name-error").textContent = "Name is required";
                isValid = false;
            } else if (name.length < 3) {
                document.getElementById("name-error").textContent = "Name must be at least 3 characters";
                isValid = false;
            }

            // Validate phone number (must be exactly 10 digits)
            if (phone === "") {
                document.getElementById("phone-error").textContent = "Phone number is required";
                isValid = false;
            }

            // Validate email format (if provided)
            if (email !== "" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById("email-error").textContent = "Invalid email format";
                isValid = false;
            }

            // Prevent form submission if validation fails
            if (!isValid) event.preventDefault();
        });
    </script>
</body>

</html>