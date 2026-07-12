<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Check if order ID is set in URL parameter or session
if (isset($_GET['id'])) {
    $orderID = $_GET['id'];
} elseif (isset($_SESSION['last_order_id'])) {
    $orderID = $_SESSION['last_order_id'];
} else {
    // If no order ID is found, redirect to orders list
    header("Location: history.php");
    exit;
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "mydatabase";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch order details - also verify that the order belongs to the current user for security
$user_id = $_SESSION['user_id'] ?? 0;
$sql = "SELECT * FROM combined_form WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

$stmt->close();
$conn->close();

// Check if order details were found
if (!$order) {
    $_SESSION['error'] = "Order not found or you don't have permission to view it.";
    header("Location: history.php");
    exit;
}

// Extract data
$createdAtDate = date("Y-m-d", strtotime($order['created_at']));

// Get the pages info
$printPagesOption = $order['pages'];
$customPages = $order['custom_pages'];

// Format the page type string for display
switch ($printPagesOption) {
    case 'odd':
        $pageType = 'Odd pages only';
        break;
    case 'even':
        $pageType = 'Even pages only';
        break;
    case 'all':
        $pageType = 'All pages';
        break;
    case 'custom':
        $pageType = 'Custom pages - ' . htmlspecialchars($customPages);
        break;
    default:
        $pageType = 'N/A';
        break;
}

// Get total pages and cost from database
$totalPages = $order['total_pages'] ?? 'N/A';
$totalCost = $order['total_cost'] ?? 0;

// Function to generate downloadable file links
function generateDownloadLinks($files) {
    if (empty($files)) {
        return 'No files available';
    }
    
    $filePaths = json_decode($files, true);
    if (!is_array($filePaths)) {
        return 'File information not available';
    }
    
    $links = '';
    foreach ($filePaths as $file) {
        $filePath = 'uploads/' . $file; // Adjust path as needed
        if (file_exists($filePath)) {
            $fileName = htmlspecialchars($file);
            $links .= "<a href='$filePath' download>$fileName</a><br>";
        } else {
            $links .= htmlspecialchars($file) . " (file not found)<br>";
        }
    }
    return $links;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="navstyles.css"> <!-- Adjust path as needed -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .order-details {
            margin-top: 20px;
            text-align: center;
        }

        .order-details h2 {
            margin-bottom: 10px;
        }

        .order-details p {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }

        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            width: 30%;
        }

        td {
            width: 70%;
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 15px;
        }

        .button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
            font-weight: bold;
        }

        .status-pending {
            background-color: #ffc107;
        }

        .status-processing {
            background-color: #17a2b8;
        }

        .status-completed {
            background-color: #28a745;
        }

        .status-cancelled {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1 style="text-align: center;">Order Details</h1>
        
        <table>
            <tr>
                <th>Order ID</th>
                <td><?php echo htmlspecialchars($order['id']); ?></td>
            </tr>
            <tr>
                <th>Date</th>
                <td><?php echo htmlspecialchars($createdAtDate); ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <?php 
                    $status = htmlspecialchars($order['status']);
                    $statusClass = 'status-' . strtolower($status);
                    echo "<span class='status $statusClass'>$status</span>";
                    ?>
                </td>
            </tr>
            <tr>
                <th>Copies</th>
                <td><?php echo htmlspecialchars($order['copies']); ?></td>
            </tr>
            <tr>
                <th>Print Pages</th>
                <td><?php echo htmlspecialchars($pageType); ?></td>
            </tr>
            <tr>
                <th>Color</th>
                <td><?php echo $order['color'] ? 'Yes' : 'No'; ?></td>
            </tr>
            <tr>
                <th>Orientation</th>
                <td><?php echo htmlspecialchars($order['orientation']); ?></td>
            </tr>
            <tr>
                <th>Sides</th>
                <td><?php echo htmlspecialchars($order['sides']); ?></td>
            </tr>
            <tr>
                <th>Paper Type</th>
                <td><?php echo htmlspecialchars($order['paper_type']); ?></td>
            </tr>
            <?php if (!empty($order['message'])): ?>
            <tr>
                <th>Message</th>
                <td><?php echo nl2br(htmlspecialchars($order['message'])); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <th>Files</th>
                <td><?php echo generateDownloadLinks($order['files']); ?></td>
            </tr>
            <tr>
                <th>Total Pages</th>
                <td><?php echo htmlspecialchars($totalPages); ?></td>
            </tr>
            <tr>
                <th>Total Cost</th>
                <td>Rs. <?php echo number_format($totalCost, 2); ?></td>
            </tr>
        </table>

        <div class="order-details">
            <?php if ($order['status'] === 'pending' || $order['status'] === 'processing'): ?>
                <h2>Thank you for your order!</h2>
                <p>We will process your order soon.</p>
            <?php elseif ($order['status'] === 'completed'): ?>
                <h2>Your order has been completed!</h2>
                <p>Thank you for using our printing service.</p>
            <?php elseif ($order['status'] === 'cancelled'): ?>
                <h2>This order has been cancelled.</h2>
                <p>Please contact us if you have any questions.</p>
            <?php endif; ?>
        </div>

        <div class="button-container">
            <a href="printer_form.php" class="button">Order Again</a>
            <a href="history.php" class="button">Order History</a>
        </div>
    </div>
</body>
</html>