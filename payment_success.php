<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Check if payment ID and order ID are provided
if (!isset($_GET['payment_id']) || !isset($_GET['order_id'])) {
    header("Location: student.php");
    exit;
}

$payment_id = $_GET['payment_id'];
$order_id = $_GET['order_id'];

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

// Update payment status in the database
$sql = "UPDATE payments SET payment_id = ?, status = 'success' WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $payment_id, $order_id);

$success = $stmt->execute();

$stmt->close();
$conn->close();

// Clear payment session data
unset($_SESSION['payment_details']);
unset($_SESSION['order_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
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
            text-align: center;
        }
        
        h1 {
            color: #28a745;
            margin-bottom: 20px;
        }
        
        .success-animation {
            margin: 40px auto;
             width: 200px;
            height: 130px;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .checkmark {
            width: 100px;
            height:100px;
            border-radius: 50%;
             display: block;
            stroke-width: 7;
            padding:20px;
            stroke: #28a745;
            stroke-miterlimit: 6;
            box-shadow: inset 0px 0px 0px #28a745;
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        }
        
        .checkmark__circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 1;
            stroke-miterlimit: 5;
            stroke: #28a745;
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }
        
        .checkmark__check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }
        
        @keyframes stroke {
            100% {
                stroke-dashoffset: 0;
            }
        }
        
        @keyframes scale {
            0%, 100% {
                transform: none;
            }
            50% {
                transform: scale3d(1.1, 1.1, 1);
            }
        }
        
        @keyframes fill {
            100% {
                box-shadow: inset 0px 0px 0px 30px #28a745;
            }
        }
        
        .payment-details {
            margin: 30px auto;
            max-width: 400px;
            text-align: left;
        }
        
        .payment-details div {
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        
        .payment-details span {
            font-weight: bold;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            margin-top: 20px;
        }
        
        .btn:hover {
            background-color: #0056b3;
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
        <h1>Payment Successful!</h1>
        
        <div class="success-animation">
            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                 <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
        </div>
        
        <p>Your payment has been processed successfully.</p>
        <p style="color:red;font-weight:bold;">DISCLAIMER <br>
        *Don't go to the previous page once payment is made Click proceed next and wait until the status is set to "Ready to Collect"</p>
        
        <div class="payment-details">
            <div>
                <span>Order ID:</span>
                <span><?php echo htmlspecialchars($order_id); ?></span>
            </div>
            <div>
                <span>Payment ID:</span>
                <span><?php echo htmlspecialchars($payment_id); ?></span>
            </div>
        </div>
        
        <a href="order.php" id="proceed" class="btn">Proceed Next</a>
    </div>
    <script>
let proceed = document.getElementById("proceed");
setTimeout(() => {
    proceed.click();
}, 1700);
    </script>
</body>
</html>