<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Check if form data is submitted
if (!isset($_POST['formData']) || !isset($_POST['totalCost'])) {
    header("Location: student.php");
    exit;
}

// Get form data and total cost
$formData = json_decode($_POST['formData'], true);
$totalCost = floatval($_POST['totalCost']);
$totalPages = intval($_POST['totalPages']);
$pageOption = $_POST['pageOption'];
$id = isset($formData['order_id']) ? intval($formData['order_id']) : 0;

// Store payment details in session for later use
$_SESSION['payment_details'] = [
    'amount' => $totalCost,
    'pages' => $totalPages,
    'page_option' => $pageOption,
    'form_data' => $formData
];

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "mydatabase"; // Using your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// First, update the combined_form table with total_pages and total_cost
if ($id > 0) {
    
}

// Check if the users table exists, if not create it
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contact VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    echo "Error creating users table: " . $conn->error;
    exit;
}

// Create payments table if it doesn't exist - WITHOUT foreign key constraint for now
$sql = "CREATE TABLE IF NOT EXISTS payments (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    order_id VARCHAR(255) NOT NULL,
    payment_id VARCHAR(255),
    amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'pending',
    pages INT(11) NOT NULL,
    copies INT(11) NOT NULL,
    paper_type VARCHAR(50) NOT NULL,
    orientation VARCHAR(50) NOT NULL,
    sides VARCHAR(50) NOT NULL,
    color TINYINT(1) NOT NULL,
    page_option VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    echo "Error creating table: " . $conn->error;
    exit;
}

// Check if the user exists in the database
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'guest@example.com';

// Let's first check if the user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// If user doesn't exist, create one
if ($result->num_rows == 0) {
    // Create a default password (you should use a more secure method in production)
    $default_password = password_hash("defaultpassword", PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $default_password, $email);
    $stmt->execute();
    
    // Get the new user's ID
    $user_id = $conn->insert_id;
} else {
    $row = $result->fetch_assoc();
    $user_id = $row['id'];
}

$stmt->close();

// Generate a unique order ID
$order_id = 'ORDER_' . time() . '_' . rand(1000, 9999);

// Insert initial payment record
$color = isset($formData['color']) ? ($formData['color'] ? 1 : 0) : 0;
$copies = isset($formData['copies']) ? intval($formData['copies']) : 1;
$paper_type = isset($formData['paper_type']) ? $conn->real_escape_string($formData['paper_type']) : 'A4';
$orientation = isset($formData['orientation']) ? $conn->real_escape_string($formData['orientation']) : 'portrait';
$sides = isset($formData['sides']) ? $conn->real_escape_string($formData['sides']) : 'single';

$sql = "INSERT INTO payments (user_id, order_id, amount, pages, copies, paper_type, orientation, sides, color, page_option)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isdiissssi", $user_id, $order_id, $totalCost, $totalPages, $copies, $paper_type, $orientation, $sides, $color, $pageOption);

if (!$stmt->execute()) {
    echo "Error: " . $stmt->error;
    exit;
}

$stmt->close();
$updateStmt = $conn->prepare("UPDATE combined_form SET total_pages = ?, total_cost = ?,order_id = ?, updated_at = CURRENT_TIMESTAMP() WHERE id = ?");
    $updateStmt->bind_param("idsi", $totalPages, $totalCost,$order_id, $id);
    
    if (!$updateStmt->execute()) {
        // Log error but continue with payment process
        error_log("Error updating combined_form: " . $updateStmt->error);
    }
    
    $updateStmt->close();
$conn->close();

// Store order_id in session
$_SESSION['order_id'] = $order_id;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General header styles */
header {
    background-color: #ffffff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 100;
    width: 100%;
    box-sizing: border-box;
}

.logo-title {
    display: flex;
    align-items: center;
}

.logo {
    height: 40px;
    margin-right: 15px;
}

.logo-title h1 {
    font-size: 18px;
    margin: 0;
    color: #333;
}

/* Navigation styles */
nav ul {
    list-style: none;
    display: flex;
    margin: 0;
    padding: 0;
    align-items: center;
}

nav li {
    margin: 0 10px;
    position: relative;
}

nav a {
    text-decoration: none;
    color: #333;
    font-weight: 500;
    padding: 10px;
    display: block;
    transition: color 0.3s;
}

nav a:hover {
    color: #4a90e2;
}

/* Profile icon styles */
.profile-dropdown {
    position: relative;
}

.profile-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s;
}

.profile-icon:hover {
    transform: scale(1.05);
}

.profile-icon:active {
    transform: scale(0.95);
}

.profile-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Dropdown menu - Hidden by default */
.dropdown-menu {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    background-color: white;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    width: 220px;
    display: none;
    opacity: 0;
    visibility: hidden;
    z-index: 1000;
    overflow: hidden;
    transition: all 0.3s ease;
}

/* Show the dropdown menu when class 'show' is present */
.dropdown-menu.show {
    display: block;
    opacity: 1;
    visibility: visible;
    animation: fadeInUp 0.3s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Username display in dropdown */
.username-display {
    padding: 15px;
    font-weight: bold;
    color: #333;
    background-color: #f9f9f9;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    font-size: 15px;
}

.username-display i {
    margin-right: 10px;
    color: #4a90e2;
    font-size: 16px;
}

.dropdown-item {
    padding: 12px 15px;
    display: flex;
    align-items: center;
    color: #333;
    transition: all 0.2s;
    text-decoration: none;
    border-bottom: 1px solid #f0f0f0;
}

.dropdown-item:last-child {
    border-bottom: none;
}

.dropdown-item i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
    font-size: 16px;
}

.dropdown-item:hover {
    background-color: #f5f5f5;
}

/* Logout button styling */
.logout-btn {
    color: #e74c3c;
}

.logout-btn i {
    color: #e74c3c;
}

.logout-btn:hover {
    background-color: #ffeeee;
    color: #c0392b;
}

/* Arrow pointing to profile icon */
.dropdown-menu::before {
    content: '';
    position: absolute;
    top: -8px;
    right: 15px;
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-bottom: 8px solid white;
}

/* Responsive styles */
@media (max-width: 900px) {
    .logo-title h1 {
        font-size: 16px;
    }
}

@media (max-width: 768px) {
    header {
        flex-direction: column;
        padding: 10px;
    }

    .logo-title {
        margin-bottom: 12px;
        text-align: center;
        justify-content: center;
    }

    .logo-title h1 {
        font-size: 15px;
    }

    nav {
        width: 100%;
    }

    nav ul {
        flex-wrap: wrap;
        justify-content: center;
    }

    nav li {
        margin: 5px 8px;
    }
    
    .dropdown-menu {
        width: 200px;
        right: 50%;
        transform: translateX(50%);
    }
    
    .dropdown-menu::before {
        right: 50%;
        margin-right: -8px;
    }
    
    .dropdown-menu.show {
        animation: fadeInUpMobile 0.3s ease-out forwards;
    }
    
    @keyframes fadeInUpMobile {
        from {
            opacity: 0;
            transform: translateY(10px) translateX(50%);
        }
        to {
            opacity: 1;
            transform: translateY(0) translateX(50%);
        }
    }
}

@media (max-width: 480px) {
    .logo {
        height: 30px;
        margin-right: 10px;
    }
    
    .logo-title h1 {
        font-size: 13px;
    }

    nav a {
        padding: 8px;
        font-size: 13px;
    }
    
    .profile-icon {
        width: 35px;
        height: 35px;
    }
    
    .dropdown-menu {
        width: 180px;
    }
    
    .dropdown-item {
        padding: 10px 12px;
        font-size: 13px;
    }
    
    .username-display {
        padding: 12px;
        font-size: 13px;
    }
}

@media (max-width: 380px) {
    nav li {
        margin: 3px 5px;
    }
    
    nav a {
        padding: 6px;
        font-size: 12px;
    }
    
    .logo-title h1 {
        font-size: 12px;
    }
    
    .dropdown-menu {
        width: 160px;
    }
}
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        
        .payment-details {
            margin-bottom: 30px;
        }
        
        .payment-details table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .payment-details th, .payment-details td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .payment-details th {
            background-color: #f2f2f2;
            font-weight: bold;
            width: 40%;
        }
        
        .amount {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            color: #007bff;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            margin-top: 20px;
        }
        
        .btn:hover {
            background-color: #0056b3;
        }
        
        .razorpay-payment-button {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 15px;
            }
        }
        
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>
    
    <div class="container">
        <h1>Payment Gateway</h1>
        
        <div class="payment-details">
            <table>
                <tr>
                    <th>Order ID</th>
                    <td><?php echo htmlspecialchars($order_id); ?></td>
                </tr>
                <tr>
                    <th>Pages</th>
                    <td><?php echo htmlspecialchars($totalPages); ?></td>
                </tr>
                <tr>
                    <th>Copies</th>
                    <td><?php echo htmlspecialchars($copies); ?></td>
                </tr>
                <tr>
                    <th>Paper Type</th>
                    <td><?php echo htmlspecialchars($paper_type); ?></td>
                </tr>
                <tr>
                    <th>Color</th>
                    <td><?php echo $color ? 'Yes' : 'No'; ?></td>
                </tr>
            </table>
        </div>
        
        <div class="amount">
            Amount: Rs. <?php echo number_format($totalCost, 2); ?>
        </div>
        
        <div id="payment-button">
            <button id="rzp-button" class="btn">Pay Now</button>
        </div>
    </div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
       // In payment.php, replace this line in the script section:
document.getElementById('rzp-button').onclick = function(e) {
    var options = {
        "key": "rzp_test_OPvjzi49BBCu8p", // Updated with your correct API key
        "amount": "<?php echo $totalCost * 100; ?>", // Amount in paise
        "currency": "INR",
        "name": "Student Printing Service",
        "description": "Payment for printing service",
        "order_id": "", // Leave blank for UPI gateway
        "handler": function (response) {
            // Send payment details to the server
            document.location.href = 'payment_success.php?payment_id=' + response.razorpay_payment_id + '&order_id=<?php echo $order_id; ?>';
        },
        "prefill": {
            "name": "<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>",
            "email": "<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>",
            "contact": "<?php echo isset($_SESSION['contact']) ? htmlspecialchars($_SESSION['contact']) : ''; ?>"
        },
        "theme": {
            "color": "#007bff"
        },
        "modal": {
            "ondismiss": function() {
                console.log('Payment dismissed');
            }
        }
    };
    
    var rzp = new Razorpay(options);
    rzp.open();
    e.preventDefault();
}
    </script>
</body>
</html>