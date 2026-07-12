<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Redefine generateDownloadLink function
function generateDownloadLink($files) {
    $filePaths = json_decode($files, true);
    $links = '';
    foreach ($filePaths as $path) {
        $fileName = basename($path);
        $links .= "<a href='$path' download>$fileName</a><br>";
    }
    return $links;
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "mydatabase"; // Change to your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get current user's ID from session
$user_id = $_SESSION['username']; // Assuming you store user_id in session

// Fetch only orders belonging to the current user
$sql = "SELECT id, created_at, files, total_cost, total_pages,status FROM combined_form WHERE username = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Order History</title>
    <link rel="stylesheet" href="styles.css"> <!-- Adjust path as needed -->
    <style>
        /* General header styles */
header {
    background-color: #ffffff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position:static;
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
    max-width: 800px;
    margin: 20px auto 0 auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    flex: 1; /* This ensures the container takes up available space, pushing the footer down */
}

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }
        .footer {
    background-color: #333;
    color: white;
    text-align: center;
    padding: 10px;
    width: 100%;
    position: fixed;
    bottom: 0;
    left: 0;
}

.footer a {
    color: #4a90e2;
    text-decoration: none;
}

.footer a:hover {
    text-decoration: underline;
}
#floater {
            display: none;
            position: absolute;
            bottom: 60px;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            z-index: 100;
            text-align: center;
            transition: opacity 0.3s ease-in-out;
             
            flex-direction:column;
            align-items:flex-start;
        }
        #floater h1 {
            font-size: 16px;
            margin: 8px 0;
            font-weight: normal;
        }
        #develop {
            cursor: pointer;
            transition: color 0.3s;
        }
        #develop:hover {
            color: #4a90e2;
        }
</style>
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container">
    <h1 style="text-align: center;">My Order History</h1>
    <p style="color:red;font-weight:bold;text-align:center;font-size:20px;">*For new orders ,Go to Dashboard*</p>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Files</th>
                <th>Total Cost</th>
                <th>Total Pages</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td><?php echo generateDownloadLink($row['files']); ?></td>
                        <td>₹<?php echo number_format($row['total_cost'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['total_pages']); ?></td>
                        <td>
    <?php 
    $status = htmlspecialchars($row['status']); 
    if (strpos($status, 'rejected') !== false) {
        echo "<span style='color: red;'>{$status}</span>";  
    } elseif ($status == 'READY') {
        echo "<span style='color: green;'>Ready to collect</span>";
    } else {
        echo "<span >Pending</span>";
    }
    ?>
</td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">You have no orders yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="footer">
    <p>&copy; 2025 PSG Institute of Technology and Applied Research. All rights reserved.</p>
    <p id="develop"><strong >DEVELOPED BY SDC </strong></p>
        <div id="floater">
        <h1>Abinaya Devadarshini D - 22CSE</h1>
            <h1>Sangamithra Saravanan - 22CSE</h1>
            <h1>Soorya S S - 23CSBS</h1>
            <h1>Karthika S - 22CSE</h1>
            <h1>Madhumitha - 22CSE</h1>
            <h1>Hemanth R - 23CSBS</h1>
        </div>
</div>
<script>
    let developTag= document.getElementById('develop');
    let floaterTag= document.getElementById('floater');
console.log("kkk");

    developTag.addEventListener('mouseover',()=>{
        
        
        floaterTag.style.display="flex";
    },true);
    developTag.addEventListener('mouseout',()=>{
         
        floaterTag.style.display="none";
    },true)
</script>
</body>
</html>