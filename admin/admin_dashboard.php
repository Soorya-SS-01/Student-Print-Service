<?php
session_start();
include('connection.php');

// Check if admin is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Function to safely get array value
function safeGetValue($array, $key, $default = '') {
    return isset($array[$key]) ? $array[$key] : $default;
}

// Process accept/reject actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    // Get the record from combined_form
    $select_sql = "SELECT * FROM combined_form WHERE id=?";
    $stmt = $conn->prepare($select_sql);
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if (!$row) {
        die("No record found for ID $id");
    }

    if ($action == 'accept') {
        // Update order status to "ready"
        $update_sql = "UPDATE orders SET status='completed ready to get' WHERE id=?";
        $stmt = $conn->prepare($update_sql);
        
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // Insert into accepted table
        $insert_sql = "INSERT INTO accepted (id, name, files, color, orientation, copies, sides, paper_type, pages, 
                       custom_pages, total_pages, total_cost, message, payment_status, status, created_at, accepted_at) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($insert_sql);
        
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        
        // Create variables for binding
        $username = safeGetValue($row, 'username');
        $files = safeGetValue($row, 'files');
        $color = safeGetValue($row, 'color');
        $orientation = safeGetValue($row, 'orientation');
        $copies = safeGetValue($row, 'copies');
        $sides = safeGetValue($row, 'sides');
        $paper_type = safeGetValue($row, 'paper_type');
        $pages = safeGetValue($row, 'pages');
        $custom_pages = safeGetValue($row, 'custom_pages');
        $total_pages = safeGetValue($row, 'total_pages');
        $total_cost = safeGetValue($row, 'total_cost');
        $message = safeGetValue($row, 'message');
        $payment_status = 'success';
        $status = 'Ready to Collect';
        $created_at = safeGetValue($row, 'created_at');
        
        $stmt->bind_param("ississsssisdssss",
            $id,
            $username, 
            $files, 
            $color, 
            $orientation, 
            $copies, 
            $sides, 
            $paper_type, 
            $pages, 
            $custom_pages, 
            $total_pages, 
            $total_cost, 
            $message,
            $payment_status,
            $status,
            $created_at
        );
        
        $stmt->execute();
        
        // Update combined_form status
        $update_sql = "UPDATE combined_form SET status='READY' WHERE id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // Redirect back to the page
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } elseif ($action == 'reject') {
        // Get the rejection reason
        $reason = $_POST['reason'];
        if ($reason === 'Other' && isset($_POST['other_reason'])) {
            $reason = $_POST['other_reason'];
        }
        
        // Update order status to "rejected"
        $update_sql = "UPDATE orders SET status='rejected' WHERE id=?";
        $stmt = $conn->prepare($update_sql);
        
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // Insert into reject table
        $insert_sql = "INSERT INTO reject (id, name, files, color, orientation, copies, sides, paper_type, pages, 
                       custom_pages, total_pages, total_cost, message, payment_status, status, created_at, rejected_at) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($insert_sql);
        
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        
        // Create variables for binding
        $username = safeGetValue($row, 'username');
        $files = safeGetValue($row, 'files');
        $color = safeGetValue($row, 'color');
        $orientation = safeGetValue($row, 'orientation');
        $copies = safeGetValue($row, 'copies');
        $sides = safeGetValue($row, 'sides');
        $paper_type = safeGetValue($row, 'paper_type');
        $pages = safeGetValue($row, 'pages');
        $custom_pages = safeGetValue($row, 'custom_pages');
        $total_pages = safeGetValue($row, 'total_pages');
        $total_cost = safeGetValue($row, 'total_cost');
        $message = safeGetValue($row, 'message');
        $payment_status = 'rejected';
        $status = 'Rejected: ' . $reason;
        $created_at = safeGetValue($row, 'created_at');
        
        $stmt->bind_param("ississsssisdssss", 
            $id,
            $username, 
            $files, 
            $color, 
            $orientation, 
            $copies, 
            $sides, 
            $paper_type, 
            $pages, 
            $custom_pages, 
            $total_pages, 
            $total_cost, 
            $message,
            $payment_status,
            $status,
            $created_at
        );
        
        $stmt->execute();
        
        // Update combined_form status
        $update_sql = "UPDATE combined_form SET status='rejected' WHERE id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // Redirect back to the page
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Get filter value from URL parameter
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'pending';

// DEBUG: Check tables independently first
$debug_mode = false;  // Set to false to disable debugging

if ($debug_mode) {
    // Check if combined_form table has records
    $cf_check = "SELECT COUNT(*) as cf_count FROM combined_form";
    $cf_result = $conn->query($cf_check);
    $cf_count = $cf_result->fetch_assoc()['cf_count'];
    
    // Check if payments table has records
    $p_check = "SELECT COUNT(*) as p_count FROM payments";
    $p_result = $conn->query($p_check);
    $p_count = $p_result->fetch_assoc()['p_count'];
}

// Try a simple LEFT JOIN instead (shows all combined_form records even without payments)
// Base query without WHERE clause
$sql = "SELECT DISTINCT
    cf.id, 
    cf.username, 
    cf.files, 
    cf.status AS order_status, 
    cf.total_pages, 
    p.order_id, 
    p.payment_id, 
    p.amount, 
    p.status AS payment_status,
    p.copies, 
    p.paper_type, 
    p.orientation, 
    p.sides, 
    p.color, 
    p.created_at 
FROM 
    combined_form cf 
JOIN 
    payments p ON cf.order_id = p.order_id WHERE p.status = 'success'";

 

$sql .= " ORDER BY cf.created_at DESC";

// Execute the query
$result = $conn->query($sql);

// Check for SQL errors
if (!$result) {
    echo "Error in SQL query: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order and Payment Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding-top: 56px;
        }
        .container {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .btn-container {
            display: flex;
            gap: 10px;
        }
        .custom-btn {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            border: 2px solid transparent;
            cursor: pointer;
        }
        .accept-btn {
            color: #4CAF50;
            border-color: #4CAF50;
            background-color: white;
        }
        .reject-btn {
            color: #F44336;
            border-color: #F44336;
            background-color: white;
        }
        .accept-btn:hover, .reject-btn:hover {
            background-color: #f1f1f1;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 5px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover, .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .filter-section {
            margin-bottom: 20px;
            display: flex;
            justify-content: flex-end;
        }
        .debug-info {
            background-color: #ffe6e6;
            border: 1px solid #ff9999;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php include('navbar1.php'); ?>
    <div class="container">
        <h1 class="text-center">Order and Payment Details</h1>
        
        
        
        
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Files</th>
                   
                    <th>Payment ID</th>
                    <th>Amount</th>
                    <th>Payment Status</th>
                    <th>Pages</th>
                    <th>Copies</th>
                    <th>Paper Type</th>
                    <th>Orientation</th>
                    <th>Sides</th>
                    <th>Color</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars(safeGetValue($row, "id")) . "</td>";
                        echo "<td>" . htmlspecialchars(safeGetValue($row, "username")) . "</td>";
                        
                        // Handle files (stored as JSON)
                        $files = json_decode(safeGetValue($row, "files"), true);
                        if ($files && is_array($files)) {
                            echo "<td>";
                            foreach ($files as $file) {
                                $fileUrl = rawurlencode($file);
                                echo "<a href='print.php?id=" . htmlspecialchars(safeGetValue($row, 'id')) . "&file=" . $fileUrl . "' target='_blank'>" . htmlspecialchars($file) . "</a><br>";
                            }
                            echo "</td>";
                        } else {
                            echo "<td>No files</td>";
                        }
                        
                        
                        echo "<td>" . htmlspecialchars(safeGetValue($row, "payment_id")) . "</td>";
                        echo "<td>₹" . htmlspecialchars(safeGetValue($row, "amount")) . "</td>";
                        echo "<td>" . htmlspecialchars(safeGetValue($row, "payment_status")) . "</td>";
                        echo "<td>" . htmlspecialchars(safeGetValue($row, "total_pages")) . "</td>";
                        echo "<td>" . htmlspecialchars(safeGetValue($row, "copies")) . "</td>";
                        echo "<td>" . htmlspecialchars(safeGetValue($row, "paper_type")) . "</td>";
                        echo "<td>" . htmlspecialchars(safeGetValue($row, "orientation")) . "</td>";
                        echo "<td>" . htmlspecialchars(safeGetValue($row, "sides")) . "</td>";
                        echo "<td>" . (safeGetValue($row, "color") == 1 ? "Color" : "Black & White") . "</td>";
                        echo "<td>" . htmlspecialchars(safeGetValue($row, "created_at")) . "</td>";
                        
                        // Action buttons - only show for pending orders
                        echo "<td>";
                        if (safeGetValue($row, "order_status") === 'pending') {
                            echo "<div class='btn-container'>";
                            
                            // Accept button
                            echo "<form method='POST' action='" . $_SERVER['PHP_SELF'] . "'>";
                            echo "<input type='hidden' name='id' value='" . htmlspecialchars(safeGetValue($row, 'id')) . "'>";
                            echo "<button type='submit' name='action' value='accept' class='custom-btn accept-btn'>Ready</button>";
                            echo "</form>";
                            
                            // Reject button - opens modal
                            echo "<button class='custom-btn reject-btn' onclick='openRejectModal(" . htmlspecialchars(safeGetValue($row, "id")) . ")'>Reject</button>";
                            
                            echo "</div>";
                        } else {
                            echo "<span class='badge " . (safeGetValue($row, "order_status") === 'READY' ? "bg-success" : "bg-danger") . "'>" . 
                                htmlspecialchars(ucfirst(safeGetValue($row, "order_status"))) . "</span>";
                        }
                        echo "</td>";
                        
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='16'>No matching records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <!-- Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeRejectModal()">&times;</span>
            <h2>Reject Order</h2>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" id="rejectOrderId" name="id" value="">
                <div class="mb-3">
                    <label for="reason" class="form-label">Rejection Reason:</label>
                    <select name="reason" id="reason" class="form-select" required>
                        <option value="">Select Reason</option>
                        <option value="Paper out of stock">Paper out of stock</option>
                        <option value="Power off">Power off</option>
                        <option value="Printer problem">Printer problem</option>
                        <option value="Improper alignment">Improper alignment</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3" id="otherReasonDiv" style="display:none;">
                    <label for="otherReason" class="form-label">Specify reason:</label>
                    <input type="text" class="form-control" id="otherReason" name="other_reason">
                </div>
                <button type="submit" name="action" value="reject" class="btn btn-danger">Reject Order</button>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Modal functions
        const modal = document.getElementById('rejectModal');
        
        function openRejectModal(orderId) {
            document.getElementById('rejectOrderId').value = orderId;
            modal.style.display = "block";
        }
        
        function closeRejectModal() {
            modal.style.display = "none";
        }
        
        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                closeRejectModal();
            }
        }
        
        // Show/hide "Other" reason text field
        document.getElementById('reason').addEventListener('change', function() {
            if (this.value === 'Other') {
                document.getElementById('otherReasonDiv').style.display = 'block';
            } else {
                document.getElementById('otherReasonDiv').style.display = 'none';
            }
        });
    </script>
</body>
</html>