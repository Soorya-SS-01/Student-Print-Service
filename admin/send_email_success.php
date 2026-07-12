<?php
require './config.php'; // Include your database configuration
require 'C:/xampp1/htdocs/newproj/admin/vendor/autoload.php'; // Include PHPMailer

// Use the PHPMailer classes with the correct namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $username = $_POST['username'] ?? '';
    $message = $_POST['message'] ?? ''; // Message to include in the email

    if (!empty($username) && !empty($message)) {
        // Query to fetch the email from the signup table
        $query = "SELECT email FROM signup WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $recipientEmail = $row['email'];
            $subject = ($action === 'success') ? 'Print Order Successful' : 'Print Order Rejected';

            // Configure and send email with PHPMailer
            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'abinayadev04@gmail.com';
            $mail->Password = 'plgb icaz nwzm pxwg'; // Replace with your app-specific password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('abinayadev04@gmail.com', 'admin'); // Set your sender email and name
            $mail->addAddress($recipientEmail); // Add recipient's email
            $mail->Subject = $subject;
            $mail->Body = $message;

            if ($mail->send()) {
                echo "Email sent successfully to " . htmlspecialchars($recipientEmail);
            } else {
                echo "Error: " . $mail->ErrorInfo;
            }
        } else {
            echo "Error: No email found for the provided username.";
        }

        $stmt->close();
    } else {
        echo "Error: Username or message is missing.";
    }

    $conn->close();
}
?>
