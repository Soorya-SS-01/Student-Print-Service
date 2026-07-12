<?php
session_start();
require './config.php'; // Include your database configuration

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Ensure the file and id parameters are provided
if (isset($_GET['file']) && isset($_GET['id'])) {
    $file = rawurldecode($_GET['file']); // Decode the file name
    $filePath = '../uploads/' . $file; // Path to the file from the admin directory
    $id = intval($_GET['id']); // Get the ID

    // Fetch username from the combined_form table using the ID
    $query = "SELECT username FROM combined_form WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $username = $row['username'];
    } else {
        die("Username not found for the specified ID.");
    }

    $stmt->close();
} else {
    die("File or ID not specified.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print File</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
    <script>
         function initiatePrint() {
            const iframe = document.querySelector("iframe");

            iframe.onload = () => {
                // Ensure the print dialog opens only after iframe content is fully loaded
                setTimeout(() => { 
                    iframe.contentWindow.print(); // Trigger print on the iframe content
                }, 500); // Delay for loading iframe content
            };
        }
        window.addEventListener('afterprint', () => {
            // Send the print success email after the print dialog is closed
            fetch('send_email_success.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'success', // Specify that this is a success notification
                    username: '<?php echo htmlspecialchars($username); ?>',
                    message: 'Your print order was completed successfully.'
                })
            })
            .then(response => response.text())
            .then(result => console.log(result)) // For debugging
            .catch(error => console.error('Error:', error));
        });
    </script>
</head>
<body>
    <iframe src="<?php echo htmlspecialchars($filePath); ?>" ></iframe>
</body>
</html>
