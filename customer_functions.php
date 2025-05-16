<?php
// Include database connection
require_once 'config.php';

// Function to get all customers
function getAllCustomers()
{
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM customers ORDER BY customer_name");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return ['error' => 'Error fetching customers: ' . $e->getMessage()];
    }
}

// Function to get customer by ID
function getCustomerById($id)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return ['error' => 'Error fetching customer: ' . $e->getMessage()];
    }
}

function searchCustomers($searchTerm)
{
    global $pdo;
    try {
        // Check if search term is numeric (for ID search)
        $isNumeric = is_numeric($searchTerm);

        // Build the query with parameters
        if ($isNumeric) {
            // Search by either ID or name that contains numbers
            $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ? OR customer_name LIKE ? ORDER BY customer_name");
            $stmt->execute([$searchTerm, "%$searchTerm%"]);
        } else {
            // Search only by name
            $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_name LIKE ? ORDER BY customer_name");
            $stmt->execute(["%$searchTerm%"]);
        }

        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return ['error' => 'Error searching customers: ' . $e->getMessage()];
    }
}
// Function to add a new customer
function addCustomer($name, $address, $phone, $email = null)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO customers (customer_name, address, phone, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $address, $phone, $email]);
        return ['success' => true, 'id' => $pdo->lastInsertId()];
    } catch (PDOException $e) {
        return ['error' => 'Error adding customer: ' . $e->getMessage()];
    }
}

// Function to update an existing customer
function updateCustomer($id, $name, $address, $phone, $email = null)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE customers SET customer_name = ?, address = ?, phone = ?, email = ? WHERE customer_id = ?");
        $stmt->execute([$name, $address, $phone, $email, $id]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['error' => 'Error updating customer: ' . $e->getMessage()];
    }
}

// Function to delete a customer
function deleteCustomer($id)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM customers WHERE customer_id = ?");
        $stmt->execute([$id]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['error' => 'Error deleting customer: ' . $e->getMessage()];
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = [];

    // Sanitize inputs
    $id = isset($_POST['customer_id']) ? filter_var($_POST['customer_id'], FILTER_VALIDATE_INT) : null;
    $name = sanitize($_POST['customer_name'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $email = sanitize($_POST['email'] ?? null);

    // Perform action based on form submission
    switch ($_POST['action']) {
        case 'add':
            if (empty($name) || empty($address) || empty($phone)) {
                $response = ['error' => 'All fields are required except email'];
            } else {
                $response = addCustomer($name, $address, $phone, $email);
            }
            break;

        case 'update':
            if (!$id || empty($name) || empty($address) || empty($phone)) {
                $response = ['error' => 'All fields are required except email'];
            } else {
                $response = updateCustomer($id, $name, $address, $phone, $email);
            }
            break;

        case 'delete':
            if (!$id) {
                $response = ['error' => 'Invalid customer ID'];
            } else {
                $response = deleteCustomer($id);
            }
            break;

        default:
            $response = ['error' => 'Invalid action'];
    }

    // Return JSON response for AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Redirect for regular form submissions
    header('Location: customers.php');
    exit;
}
