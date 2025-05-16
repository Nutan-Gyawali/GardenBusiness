<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - GreenThumb Garden Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/services.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/bookings.css">
</head>

<body>
    <?php
    require_once 'customer_functions.php';
    $customers = getAllCustomers();
    $editCustomer = null;

    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $editCustomer = getCustomerById($_GET['edit']);
    }
    ?>

    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-leaf"></i>
            <span>GreenThumb</span>
        </div>
        <nav>
            <ul>
                <!-- <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li> -->
                <li><a href="services.php"><i class="fas fa-tools"></i> Services</a></li>
                <li><a href="bookings.php"><i class="fas fa-calendar-alt"></i> Bookings</a></li>
                <li><a href="customers.php" class="active"><i class="fas fa-users"></i> Customers</a></li>
               
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <header>
            <div class="header-content">
                <button id="sidebar-toggle"><i class="fas fa-bars"></i></button>
                <h1>Customers</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span>Admin</span>
                </div>
            </div>
        </header>

        <div class="content-container">
            <div class="page-header">
                <h2>Manage Customers</h2>
                <a href="addCustomers.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Customer
                </a>

            </div>

            <!-- Customer Form -->
            <div class="form-container" id="customer-form" style="display: <?php echo $editCustomer ? 'block' : 'none'; ?>">
                <div class="card">
                    <div class="card-header">
                        <h3><?php echo $editCustomer ? 'Edit Customer' : 'Add New Customer'; ?></h3>
                        <button class="close-btn" id="close-form"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="card-body">
                        <form id="customerForm" method="POST" action="customer_functions.php">
                            <input type="hidden" name="action" value="<?php echo $editCustomer ? 'update' : 'add'; ?>">
                            <?php if ($editCustomer): ?>
                                <input type="hidden" name="customer_id" value="<?php echo $editCustomer['customer_id']; ?>">
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="customer_name">Customer Name <span class="required">*</span></label>
                                <input type="text" id="customer_name" name="customer_name" class="form-control" value="<?php echo $editCustomer ? htmlspecialchars($editCustomer['customer_name']) : ''; ?>" required>
                                <span class="error-message" id="name-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="address">Address <span class="required">*</span></label>
                                <input type="text" id="address" name="address" class="form-control" value="<?php echo $editCustomer ? htmlspecialchars($editCustomer['address']) : ''; ?>" required>
                                <span class="error-message" id="address-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone <span class="required">*</span></label>
                                <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo $editCustomer ? htmlspecialchars($editCustomer['phone']) : ''; ?>" required>
                                <span class="error-message" id="phone-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?php echo $editCustomer ? htmlspecialchars($editCustomer['email'] ?? '') : ''; ?>">
                                <span class="error-message" id="email-error"></span>
                            </div>

                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary" id="cancel-form">Cancel</button>
                                <button type="submit" class="btn btn-primary"><?php echo $editCustomer ? 'Update Customer' : 'Add Customer'; ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Customers Table -->
            <div class="cards">
                <div class="card-body">
                    <div class="search-container">
                        <input type="text" id="customerSearch" class="search-input" placeholder="Search customers...">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                    
                        <table class="data-table" id="customersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($customers['error'])): ?>
                                    <tr>
                                        <td colspan="6" class="no-data">Error: <?php echo htmlspecialchars($customers['error']); ?></td>
                                    </tr>
                                <?php elseif (empty($customers)): ?>
                                    <tr>
                                        <td colspan="6" class="no-data">No customers found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($customers as $customer): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($customer['customer_id']); ?></td>
                                            <td><?php echo htmlspecialchars($customer['customer_name']); ?></td>
                                            <td><?php echo htmlspecialchars($customer['address']); ?></td>
                                            <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                            <td><?php echo htmlspecialchars($customer['email'] ?? ''); ?></td>
                                            <td class="actions">
                                                <a href="?edit=<?php echo $customer['customer_id']; ?>" class="btn-icon edit" title="Edit"><i class="fas fa-edit"></i></a>
                                                <form method="POST" action="customer_functions.php" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="customer_id" value="<?php echo $customer['customer_id']; ?>">
                                                    <button type="submit" class="btn-icon delete" title="Delete"><i class="fas fa-trash-alt"></i></button>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Form toggle
            const showAddFormBtn = document.getElementById("show-add-form");
            const customerForm = document.getElementById("customer-form");
            const closeFormBtn = document.getElementById("close-form");
            const cancelFormBtn = document.getElementById("cancel-form");

            if (showAddFormBtn) {
                showAddFormBtn.addEventListener("click", function() {
                    customerForm.style.display = "block";
                });
            }

            if (closeFormBtn) {
                closeFormBtn.addEventListener("click", function() {
                    customerForm.style.display = "none";
                    window.location.href = "customers.php";
                });
            }

            if (cancelFormBtn) {
                cancelFormBtn.addEventListener("click", function() {
                    customerForm.style.display = "none";
                    window.location.href = "customers.php";
                });
            }

            // Form validation
            /**
             * Form validation for customer form
             */
            document.getElementById("customer-form").addEventListener("submit", function(event) {
                let isValid = true;

                // Get form values and trim whitespace
                const name = document.getElementById("customer_name").value.trim();
                const address = document.getElementById("address").value.trim();
                const phone = document.getElementById("phone").value.trim();
                const email = document.getElementById("email").value.trim();

                // Clear previous error messages
                document.getElementById("name-error").textContent = "";
                document.getElementById("address-error").textContent = "";
                document.getElementById("phone-error").textContent = "";
                document.getElementById("email-error").textContent = "";

                // Validate name (must be between 3 and 100 characters)
                if (name === "") {
                    document.getElementById("name-error").textContent = "Name is required";
                    isValid = false;
                } else if (name.length < 3) {
                    document.getElementById("name-error").textContent = "Name must be at least 3 characters";
                    isValid = false;
                } else if (name.length > 100) {
                    document.getElementById("name-error").textContent = "Name cannot exceed 100 characters";
                    isValid = false;
                }

                // Validate address
                if (address === "") {
                    document.getElementById("address-error").textContent = "Address is required";
                    isValid = false;
                }

                // Validate phone number with more flexible formatting options
                if (phone === "") {
                    document.getElementById("phone-error").textContent = "Phone number is required";
                    isValid = false;
                } else {
                    // Remove all non-digit characters to check actual digit count
                    const digitsOnly = phone.replace(/\D/g, '');

                    if (digitsOnly.length !== 10) {
                        document.getElementById("phone-error").textContent = "Phone number must contain exactly 10 digits";
                        isValid = false;
                    } else if (!/^(?:\(\d{3}\)|\d{3})[-.\s]?\d{3}[-.\s]?\d{4}$/.test(phone) &&
                        !/^\d{10}$/.test(phone)) {
                        document.getElementById("phone-error").textContent = "Please use a valid format: 1234567890, 123-456-7890, (123) 456-7890";
                        isValid = false;
                    }
                }

                // Validate email format (if provided)
                if (email !== "" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    document.getElementById("email-error").textContent = "Invalid email format";
                    isValid = false;
                }

                // Prevent form submission if validation fails
                if (!isValid) event.preventDefault();
            });
            // Search functionality
            const customerSearch = document.getElementById("customerSearch");
            const customersTable = document.getElementById("customersTable");

            if (customerSearch && customersTable) {
                customerSearch.addEventListener("keyup", function() {
                    const searchValue = this.value.toLowerCase().trim();
                    const rows = customersTable.getElementsByTagName("tbody")[0].getElementsByTagName("tr");

                    for (let i = 0; i < rows.length; i++) {
                        const row = rows[i];
                        const cells = row.getElementsByTagName("td");
                        let found = false;

                        for (let j = 0; j < cells.length - 1; j++) {
                            const cellValue = cells[j].textContent.toLowerCase();
                            if (cellValue.includes(searchValue)) {
                                found = true;
                                break;
                            }
                        }

                        row.style.display = found ? "" : "none";
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
    <script src="js/script.js"></script>
</body>

</html>