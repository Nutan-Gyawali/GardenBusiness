<?php
// Include database connection
require_once 'config.php';

// Function to get all services
function getAllServices()
{
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM services ORDER BY service_name");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return ['error' => 'Error fetching services: ' . $e->getMessage()];
    }
}

// Function to get service by ID
function getServiceById($id)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM services WHERE service_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return ['error' => 'Error fetching service: ' . $e->getMessage()];
    }
}

// Function to add a new service
function addService($name, $price, $description = null)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO services (service_name, price_per_hour, description) VALUES (?, ?, ?)");
        $stmt->execute([$name, $price, $description]);
        return ['success' => true, 'id' => $pdo->lastInsertId()];
    } catch (PDOException $e) {
        return ['error' => 'Error adding service: ' . $e->getMessage()];
    }
}

// Function to update an existing service
function updateService($id, $name, $price, $description = null)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE services SET service_name = ?, price_per_hour = ?, description = ? WHERE service_id = ?");
        $stmt->execute([$name, $price, $description, $id]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['error' => 'Error updating service: ' . $e->getMessage()];
    }
}

// Function to delete a service
function deleteService($id)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM services WHERE service_id = ?");
        $stmt->execute([$id]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['error' => 'Error deleting service: ' . $e->getMessage()];
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = [];

    // Sanitize inputs
    $id = isset($_POST['service_id']) ? filter_var($_POST['service_id'], FILTER_VALIDATE_INT) : null;
    $name = sanitize($_POST['service_name'] ?? '');
    $price = isset($_POST['price_per_hour']) ? filter_var($_POST['price_per_hour'], FILTER_VALIDATE_FLOAT) : null;
    $description = sanitize($_POST['description'] ?? null);

    // Validate price format
    if ($price === false || $price === null) {
        $response = ['error' => 'Invalid price format'];
    } else {
        // Perform action based on form submission
        switch ($_POST['action']) {
            case 'add':
                if (empty($name) || $price === null) {
                    $response = ['error' => 'Service name and price are required'];
                } else {
                    $response = addService($name, $price, $description);
                }
                break;

            case 'update':
                if (!$id || empty($name) || $price === null) {
                    $response = ['error' => 'Service ID, name, and price are required'];
                } else {
                    $response = updateService($id, $name, $price, $description);
                }
                break;

            case 'delete':
                if (!$id) {
                    $response = ['error' => 'Invalid service ID'];
                } else {
                    $response = deleteService($id);
                }
                break;

            default:
                $response = ['error' => 'Invalid action'];
        }
    }

    // Return JSON response for AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Redirect for regular form submissions
    header('Location: index.php');
    exit;
}
