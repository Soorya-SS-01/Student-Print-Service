<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

// Get JSON data from request
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Validate input data
if (!isset($data['order_id']) || !isset($data['total_pages']) || !isset($data['total_cost'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$order_id = intval($data['order_id']);
$total_pages = intval($data['total_pages']);
$total_cost = floatval($data['total_cost']);

// Basic validation
if ($order_id <= 0 || $total_pages <= 0 || $total_cost <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydatabase";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? 0;

// Verify the order belongs to the current user
$checkSql = "SELECT id FROM combined_form WHERE id = ? AND user_id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("ii", $order_id, $user_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    $checkStmt->close();
    $conn->close();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Order not found or not authorized']);
    exit;
}
$checkStmt->close();

// Update the order with total pages and cost
$updateSql = "UPDATE combined_form SET total_pages = ?, total_cost = ? WHERE id = ? AND user_id = ?";
$updateStmt = $conn->prepare($updateSql);
$updateStmt->bind_param("idii", $total_pages, $total_cost, $order_id, $user_id);

// Execute update
if ($updateStmt->execute()) {
    $updateStmt->close();
    $conn->close();
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
    exit;
} else {
    $error = $updateStmt->error;
    $updateStmt->close();
    $conn->close();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $error]);
    exit;
}
?>