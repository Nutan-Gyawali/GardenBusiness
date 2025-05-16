<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - GreenThumb Garden Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/cs

pasted

I'll analyze this file for you. It appears to be an HTML page for managing services in a garden services management system called " GreenThumb Garden Services".

        Overview of the File
        This is a PHP and HTML page (services.php) that manages garden services. It includes functionality to:

        View all services in a card-based layout
        Add new services
        Edit existing services
        Delete services
        Each service has:

        Name
        Price per hour
        Description (optional)
        Auto-assigned icon based on service name keywords
        Key Components
        Structure
        Sidebar navigation with links to Dashboard, Bookings, Customers, and Services
        Main content area with a header and service management functionality
        Form for adding/editing services
        Grid display of existing services
        Functionality
        PHP backend integration with service_functions.php
        Form validation using JavaScript
        Responsive design with sidebar toggle
        Automatic icon selection based on service name
        Issues in the Code
        I noticed a few issues that should be addressed:

        JavaScript Error: There's a duplicate variable declaration - closeFormBtn and serviceForm are declared twice.
        Missing Element: The code references a button with ID "show-add-form" , but it doesn't appear to be defined in the HTML.
        Security Concerns: While the code uses htmlspecialchars() for output sanitization, you should ensure the service_functions.php file properly validates and sanitizes inputs.
        Recommended Improvements
        Would you like me to:

        Fix the JavaScript errors?
        Improve form validation?
        Add missing elements?
        Make any design improvements?
        Or would you prefer a particular aspect of this code to focus on?




        fix the errors

        I'll fix the errors in the code. The main issues are in the JavaScript section where there are duplicate variable declarations and a missing UI element.


        Fixed services.php
        Code
        I've fixed all the errors in the code. Here are the specific changes I made:

        1. Fixed the JavaScript section
        The original code had several issues in the JavaScript section:

        Duplicate variable declarations for closeFormBtn and serviceForm
        The event handler for the "Add Service" button wasn't working properly
        The variables were scattered throughout the code making it harder to maintain
        2. Added the missing ID to the "Add Service" button
        Added id="show-add-form" to the "Add Service" link to make it selectable by JavaScript
        This ensures the button click event handler can find and attach to the proper element
        3. Fixed a formatting issue in the price display
        Removed an extra curly brace in the price display: Cha



        <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Services - GreenThumb Garden Services</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="css/services.css">
    </head>

<body>
    <?php
    require_once 'service_functions.php';
    $services = getAllServices();
    $editService = null;

    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $editService = getServiceById($_GET['edit']);
    }
    ?>

    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-leaf"></i>
            <span>GreenThumb</span>
        </div>
        <nav>
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="bookings.php"><i class="fas fa-calendar-alt"></i> Bookings</a></li>
                <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
                <li><a href="services.php" class="active"><i class="fas fa-tools"></i> Services</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <header>
            <div class="header-content">
                <button id="sidebar-toggle"><i class="fas fa-bars"></i></button>
                <h1>Services</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span>Admin</span>
                </div>
            </div>
        </header>

        <div class="content-container">
            <div class="page-header">
                <h2>Manage Services</h2>
                <a href="add_service.php" class="btn btn-primary" id="show-add-form"><i class="fas fa-plus"></i> Add Service</a>
            </div>

            <!-- Service Form -->
            <div class="form-container" id="service-form" style="display: <?php echo $editService ? 'block' : 'none'; ?>">
                <div class="card">
                    <div class="card-header">
                        <h3><?php echo $editService ? 'Edit Service' : 'Add New Service'; ?></h3>
                        <button class="close-btn" id="close-form"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="card-body">
                        <form id="serviceForm" method="POST" action="service_functions.php">
                            <input type="hidden" name="action" value="<?php echo $editService ? 'update' : 'add'; ?>">
                            <?php if ($editService): ?>
                                <input type="hidden" name="service_id" value="<?php echo $editService['service_id']; ?>">
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="service_name">Service Name <span class="required">*</span></label>
                                <input type="text" id="service_name" name="service_name" class="form-control" value="<?php echo $editService ? htmlspecialchars($editService['service_name']) : ''; ?>" required>
                                <span class="error-message" id="name-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="price_per_hour">Price Per Hour ($) <span class="required">*</span></label>
                                <input type="number" id="price_per_hour" name="price_per_hour" class="form-control" step="0.01" min="0" value="<?php echo $editService ? htmlspecialchars($editService['price_per_hour']) : ''; ?>" required>
                                <span class="error-message" id="price-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="3"><?php echo $editService ? htmlspecialchars($editService['description'] ?? '') : ''; ?></textarea>
                            </div>

                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary" id="cancel-form">Cancel</button>
                                <button type="submit" class="btn btn-primary"><?php echo $editService ? 'Update Service' : 'Add Service'; ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Services Display -->
            <div class="services-grid">
                <?php if (isset($services['error'])): ?>
                    <div class="error-message"><?php echo htmlspecialchars($services['error']); ?></div>
                <?php elseif (empty($services)): ?>
                    <div class="no-data">No services found</div>
                <?php else: ?>
                    <?php foreach ($services as $service): ?>
                        <div class="service-card">
                            <div class="service-icon">
                                <?php
                                // Choose an icon based on service name
                                $icon = 'fa-hand-holding-seedling';
                                if (stripos($service['service_name'], 'lawn') !== false || stripos($service['service_name'], 'mow') !== false) {
                                    $icon = 'fa-cut';
                                } elseif (stripos($service['service_name'], 'plant') !== false) {
                                    $icon = 'fa-seedling';
                                } elseif (stripos($service['service_name'], 'hedge') !== false || stripos($service['service_name'], 'trim') !== false) {
                                    $icon = 'fa-tree';
                                } elseif (stripos($service['service_name'], 'fertiliz') !== false) {
                                    $icon = 'fa-fill-drip';
                                } elseif (stripos($service['service_name'], 'consult') !== false) {
                                    $icon = 'fa-clipboard-list';
                                }
                                ?>
                                <i class="fas <?php echo $icon; ?>"></i>
                            </div>
                            <div class="service-details">
                                <h3><?php echo htmlspecialchars($service['service_name']); ?></h3>
                                <div class="service-price">$<?php echo htmlspecialchars($service['price_per_hour']); ?> per hour</div>
                                <?php if (!empty($service['description'])): ?>
                                    <p class="service-description"><?php echo htmlspecialchars($service['description']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="service-actions">
                                <a href="?edit=<?php echo $service['service_id']; ?>" class="btn-icon edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="service_functions.php" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                                    <button type="submit" class="btn-icon delete" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Get form elements
            const serviceForm = document.getElementById("service-form");
            const closeFormBtn = document.getElementById("close-form");
            const cancelFormBtn = document.getElementById("cancel-form");
            const showAddFormBtn = document.getElementById("show-add-form");

            // Show Add Form button handling
            if (showAddFormBtn) {
                showAddFormBtn.addEventListener("click", function(e) {
                    e.preventDefault();
                    serviceForm.style.display = "block";
                });
            }

            // Close form button handling
            if (closeFormBtn) {
                closeFormBtn.addEventListener("click", function() {
                    window.location.href = "services.php";
                });
            }

            // Cancel form button handling
            if (cancelFormBtn) {
                cancelFormBtn.addEventListener("click", function() {
                    window.location.href = "services.php";
                });
            }

            // Form validation
            const form = document.getElementById("serviceForm");
            if (form) {
                form.addEventListener("submit", function(event) {
                    let isValid = true;

                    // Validate service name
                    const nameInput = document.getElementById("service_name");
                    const nameError = document.getElementById("name-error");
                    if (!nameInput.value.trim()) {
                        nameError.textContent = "Service name is required";
                        isValid = false;
                    } else {
                        nameError.textContent = "";
                    }

                    // Validate price per hour
                    const priceInput = document.getElementById("price_per_hour");
                    const priceError = document.getElementById("price-error");
                    if (!priceInput.value.trim()) {
                        priceError.textContent = "Price per hour is required";
                        isValid = false;
                    } else if (isNaN(parseFloat(priceInput.value)) || parseFloat(priceInput.value) < 0) {
                        priceError.textContent = "Please enter a valid price";
                        isValid = false;
                    } else {
                        priceError.textContent = "";
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
    <script src="js/script.js"></script>
</body>

</html>