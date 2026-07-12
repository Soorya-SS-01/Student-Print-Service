<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reject') {
    require('./config.php'); // Include your database configuration
    require 'C:\\xampp1\\htdocs\\newproj\\admin\\vendor\\autoload.php'; // Include Composer's autoloader

    $username = $_POST['username'] ?? '';
    $reason = $_POST['reason'] ?? '';

    if (!empty($username) && !empty($reason)) {
        // Query to fetch the email from the signup table
        $query = "SELECT email FROM signup WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $recipientEmail = $row['email'];
            $subject = 'Print Order Rejected';
            $message = "Your print order was rejected due to: " . htmlspecialchars($reason);

            // Configure and send email with PHPMailer
            $mail = new PHPMailer();
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
        echo "Error: Username or reason is missing.";
    }

    $conn->close();
}
?>
