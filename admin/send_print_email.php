<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection and PHPMailer
include('connection.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Check if the username and file parameters are provided
if (isset($_POST['username']) && isset($_POST['file'])) {
    $username = $_POST['username'];
    $file = $_POST['file'];

    // Fetch the user's email from the signup table
    $query = "SELECT email FROM signup WHERE username=?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $recipientEmail = $user['email'];
            $senderEmail = 'sangamithrasaravanan2003@gmail.com'; // Your sender email

            // Debug: Display the recipient and sender emails
            echo "Recipient Email: " . $recipientEmail . "<br>";
            echo "Sender Email: " . $senderEmail . "<br>";

            // Prepare the email content
            $subject = "Your file has been printed";
            $body = "Dear $username,<br>Your file <strong>$file</strong> has been successfully printed.";

            // Send email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'sangamithrasaravanan2003@gmail.com'; // Your Gmail address
                $mail->Password = 'wyufklihtwdjizcn';    // Your Gmail app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom($senderEmail, 'Admin');
                $mail->addAddress($recipientEmail);

                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $body;

                $mail->send();
                echo "Email sent successfully!";
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "User not found!";
        }
    } else {
        echo "Database query failed: " . $conn->error;
    }
} else {
    echo "Invalid request. Username or file not set.";
}
