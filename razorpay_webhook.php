<?php
// This file handles Razorpay webhook notifications
// Configure this URL in your Razorpay dashboard

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "mydatabase"; // Replace with your DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    die("Connection failed: " . $conn->connect_error);
}

// Fetch payload
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

// Verify the webhook signature (recommended in production)
// You'll need to add this code when going live

// Get the event type and related data
if (!isset($data['event'])) {
    http_response_code(400);
    exit('Invalid payload');
}

$event = $data['event'];
$payment = $data['payload']['payment']['entity'];

// Extract payment details
$payment_id = $payment['id'];
$order_id = $payment['notes']['order_id'] ?? '';
$status = '';

// Determine the status based on the event
switch ($event) {
    case 'payment.authorized':
    case 'payment.captured':
        $status = 'success';
        break;
    case 'payment.failed':
        $status = 'failed';
        break;
    case 'payment.pending':
        $status = 'pending';
        break;
    default:
        $status = 'unknown';
        break;
}

// Update the payment status in database if we have an order_id
if (!empty($order_id)) {
    $sql = "UPDATE payments SET payment_id = ?, status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $payment_id, $status, $order_id);
    
    if (!$stmt->execute()) {
        error_log("Failed to update payment: " . $stmt->error);
        http_response_code(500);
        exit('Database update failed');
    }
    
    $stmt->close();
} else {
    error_log("Missing order_id in webhook payload");
    http_response_code(400);
    exit('Missing order_id');
}

$conn->close();

// Return success
http_response_code(200);
echo 'Webhook processed successfully';
?>