<?php
// session_start();  // Add session start at the top

// Include database connection
require_once 'config.php';

// Function to get all bookings with customer and service details
function getAllBookings()
{
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT b.*, c.customer_name, s.service_name, s.price_per_hour 
            FROM bookings b
            JOIN customers c ON b.customer_id = c.customer_id
            JOIN services s ON b.service_id = s.service_id
            ORDER BY b.booking_date DESC, b.created_at DESC
        ");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return ['error' => 'Error fetching bookings: ' . $e->getMessage()];
    }
}

// Function to get booking by ID
function getBookingById($id)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT b.*, c.customer_name, s.service_name, s.price_per_hour 
            FROM bookings b
            JOIN customers c ON b.customer_id = c.customer_id
            JOIN services s ON b.service_id = s.service_id
            WHERE b.booking_id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return ['error' => 'Error fetching booking: ' . $e->getMessage()];
    }
}

// Function to get bookings by customer ID
function getBookingsByCustomerId($customerId)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT b.*, c.customer_name, s.service_name, s.price_per_hour 
            FROM bookings b
            JOIN customers c ON b.customer_id = c.customer_id
            JOIN services s ON b.service_id = s.service_id
            WHERE b.customer_id = ?
            ORDER BY b.booking_date DESC
        ");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return ['error' => 'Error fetching customer bookings: ' . $e->getMessage()];
    }
}

// Function to add a new booking
function addBooking($customerId, $serviceId, $bookingDate, $timeWorked, $status = 'pending', $notes = null)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO bookings (customer_id, service_id, booking_date, time_worked, status, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$customerId, $serviceId, $bookingDate, $timeWorked, $status, $notes]);
        return ['success' => true, 'id' => $pdo->lastInsertId()];
    } catch (PDOException $e) {
        return ['error' => 'Error adding booking: ' . $e->getMessage()];
    }
}

// Function to update an existing booking
function updateBooking($id, $customerId, $serviceId, $bookingDate, $timeWorked, $status, $notes = null)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE bookings SET customer_id = ?, service_id = ?, booking_date = ?, time_worked = ?, status = ?, notes = ? WHERE booking_id = ?");
        $stmt->execute([$customerId, $serviceId, $bookingDate, $timeWorked, $status, $notes, $id]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['error' => 'Error updating booking: ' . $e->getMessage()];
    }
}

// Function to delete a booking
function deleteBooking($id)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE booking_id = ?");
        $stmt->execute([$id]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['error' => 'Error deleting booking: ' . $e->getMessage()];
    }
}

// Helper function to sanitize input
// function sanitize($input)
// {
//     return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
// }

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = [];

    // Debug - log the received data
    error_log("Booking action received: " . $_POST['action']);
    error_log("POST data: " . print_r($_POST, true));

    // Sanitize inputs
    $id = isset($_POST['booking_id']) ? filter_var($_POST['booking_id'], FILTER_VALIDATE_INT) : null;
    $customerId = isset($_POST['customer_id']) ? filter_var($_POST['customer_id'], FILTER_VALIDATE_INT) : null;
    $serviceId = isset($_POST['service_id']) ? filter_var($_POST['service_id'], FILTER_VALIDATE_INT) : null;
    $bookingDate = sanitize($_POST['booking_date'] ?? '');
    $timeWorked = isset($_POST['time_worked']) ? filter_var($_POST['time_worked'], FILTER_VALIDATE_FLOAT) : null;
    $status = sanitize($_POST['status'] ?? 'pending');
    $notes = sanitize($_POST['notes'] ?? null);

    // Handle different actions
    switch ($_POST['action']) {
        case 'add':
            if (!$customerId || !$serviceId || empty($bookingDate) || $timeWorked === null) {
                $_SESSION['booking_error'] = 'Customer, service, date, and time worked are required';
            } else {
                $result = addBooking($customerId, $serviceId, $bookingDate, $timeWorked, $status, $notes);
                if (isset($result['error'])) {
                    $_SESSION['booking_error'] = $result['error'];
                } else {
                    $_SESSION['booking_success'] = true;
                }
            }
            break;

        case 'update':
            if (!$id || !$customerId || !$serviceId || empty($bookingDate) || $timeWorked === null) {
                $_SESSION['booking_error'] = 'Booking ID, customer, service, date, and time worked are required';
            } else {
                $result = updateBooking($id, $customerId, $serviceId, $bookingDate, $timeWorked, $status, $notes);
                if (isset($result['error'])) {
                    $_SESSION['booking_error'] = $result['error'];
                } else {
                    $_SESSION['booking_success'] = true;
                }
            }
            break;

        case 'delete':
            if (!$id) {
                $_SESSION['booking_error'] = 'Invalid booking ID';
                error_log("Delete failed: Invalid booking ID");
            } else {
                $result = deleteBooking($id);
                if (isset($result['error'])) {
                    $_SESSION['booking_error'] = $result['error'];
                    error_log("Delete failed: " . $result['error']);
                } else {
                    $_SESSION['booking_success'] = true;
                    error_log("Delete successful for booking ID: $id");
                }
            }
            break;

        default:
            $_SESSION['booking_error'] = 'Invalid action';
    }

    // Return JSON response for AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Redirect back to bookings page for regular form submissions
    header('Location: bookings.php');
    exit;
}

// If this file is accessed directly without a form submission
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {
    // This means the file was accessed directly, redirect to bookings.php
    header('Location: bookings.php');
    exit;
}
