<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Include database connection
include('connection.php');

// Function to safely get array value
function safeGetValue($array, $key, $default = '') {
    return isset($array[$key]) ? $array[$key] : $default;
}

// Get order ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("Invalid order ID");
}

// Fetch order details
$order_sql = "SELECT * FROM combined_form WHERE id=?";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die("Order not found");
}

// Fetch payment details
$payment_sql = "SELECT * FROM payments WHERE order_id=?";
$payment_stmt = $conn->prepare($payment_sql);
$payment_stmt->bind_param("i", $id);
$payment_stmt->execute();
$payment_result = $payment_stmt->get_result();
$payment = $payment_result->fetch_assoc();

// Get user details
$user_sql = "SELECT * FROM signup WHERE username=?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("s", $order['username']);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order #<?php echo $id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding-top: 56px;
        }
        .container {
            margin-top: 20px;
            max-width: 800px;
        }
        .order-details {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .detail-row {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .file-list {
            list-style-type: none;
            padding: 0;
        }
        .file-list li {
            margin-bottom: 5px;
        }
        .payment-status {
            font-size: 1.2em;
            font-weight: bold;
        }
        .status-paid {
            color: #28a745;
        }
        .status-unpaid {
            color: #dc3545;
        }
    </style>
</head>
<body>
<?php include('navbar1.php'); ?>
<div class="container">
    <h1 class="text-center mb-4">Order Details #<?php echo $id; ?></h1>
    
    <div class="order-details">
        <!-- User Information -->
        <div class="detail-row">
            <h4>User Information</h4>
            <div class="row">
                <div class="col-md-6">
                    <span class="detail-label">Username:</span>
                    <?php echo htmlspecialchars(safeGetValue($order, 'username')); ?>
                </div>
                <div class="col-md-6">
                    <span class="detail-label">Email:</span>
                    <?php echo htmlspecialchars(safeGetValue($user, 'email')); ?>
                </div>
            </div>
        </div>

        <!-- Order Information -->
        <div class="detail-row">
            <h4>Order Information</h4>
            <div class="row">
                <div class="col-md-6">
                    <span class="detail-label">Order ID:</span>
                    <?php echo htmlspecialchars(safeGetValue($order, 'id')); ?>
                </div>
                <div class="col-md-6">
                    <span class="detail-label">Created At:</span>
                    <?php echo htmlspecialchars(safeGetValue($order, 'created_at')); ?>
                </div>
            </div>
        </div>

        <!-- Print Specifications -->
        <div class="detail-row">
            <h4>Print Specifications</h4>
            <div class="row">
                <div class="col-md-4">
                    <span class="detail-label">Color:</span>
                    <?php echo htmlspecialchars(safeGetValue($order, 'color')); ?>
                </div>
                <div class="col-md-4">
                    <span class="detail-label">Orientation:</span>
                    <?php echo htmlspecialchars(safeGetValue($order, 'orientation')); ?>
                </div>
                <div class="col-md-4">
                    <span class="detail-label">Copies:</span>
                    <?php echo htmlspecialchars(safeGetValue($order, 'copies')); ?>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <span class="detail-label">Sides:</span>
                    <?php echo htmlspecialchars(safeGetValue($order, 'sides')); ?>
                </div>
                <div class="col-md-4">
                    <span class="detail-label">Paper Type:</span>
                    <?php echo htmlspecialchars(safeGetValue($order, 'paper_type')); ?>
                </div>
                <div class="col-md-4">
                    <span class="detail-label">Pages:</span>
                    <?php 
                    $custom_pages = safeGetValue($order, 'custom_pages');
                    if (empty($custom_pages) || $custom_pages == 'none' || $custom_pages == 'all') {
                        echo "All Pages (" . htmlspecialchars(safeGetValue($order, 'pages')) . " total)";
                    } else {
                        echo htmlspecialchars($custom_pages);
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Files -->
        <div class="detail-row">
            <h4>Files</h4>
            <?php
            $files = json_decode(safeGetValue($order, 'files'), true);
            if ($files && is_array($files)) {
                echo "<ul class='file-list'>";
                foreach ($files as $file) {
                    $fileUrl = rawurlencode($file);
                    echo "<li><a href='print.php?id=" . htmlspecialchars($id) . "&file=" . $fileUrl . "' target='_blank'>" . htmlspecialchars($file) . "</a></li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No files attached</p>";
            }
            ?>
        </div>

        <!-- Message -->
        <?php if (!empty(safeGetValue($order, 'message'))): ?>
        <div class="detail-row">
            <h4>Message</h4>
            <p><?php echo nl2br(htmlspecialchars(safeGetValue($order, 'message'))); ?></p>
        </div>
        <?php endif; ?>

        <!-- Payment Information -->
        <div class="detail-row">
            <h4>Payment Information</h4>
            <div class="row">
                <div class="col-md-4">
                    <span class="detail-label">Total Cost:</span>
                    ₹<?php echo htmlspecialchars(safeGetValue($order, 'total_cost')); ?>
                </div>
                <div class="col-md-4">
                    <span class="detail-label">Payment Status:</span>
                    <span class="payment-status <?php echo $payment ? 'status-paid' : 'status-unpaid'; ?>">
                        <?php echo htmlspecialchars(safeGetValue($payment, 'status', 'Not Paid')); ?>
                    </span>
                </div>
                <?php if ($payment): ?>
                <div class="col-md-4">
                    <span class="detail-label">Payment ID:</span>
                    <?php echo htmlspecialchars(safeGetValue($payment, 'payment_id')); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
</div>
</body>
</html>