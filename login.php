<?php
session_start();

// If user is already logged in, redirect to home page
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: home.php");
    exit;
}

$login = false;
$error_username = $error_password = "";

// Include database connection
include('connection.php');

// Google OAuth configuration
$client_id = 'YOUR_CLIENT_ID.apps.googleusercontent.com'; // Replace with your Google OAuth client ID
$redirect_uri = 'https://yourdomain.com/callback.php'; // Replace with your actual redirect URI
$scope = 'email profile openid'; // Scopes you want to request

// Function to redirect to Google OAuth consent screen
function redirectToGoogle() {
    global $client_id, $redirect_uri, $scope;
    $auth_url = 'https://accounts.google.com/o/oauth2/auth'
        . '?response_type=code'
        . '&client_id=' . urlencode($client_id)
        . '&redirect_uri=' . urlencode($redirect_uri)
        . '&scope=' . urlencode($scope);

    header('Location: ' . $auth_url);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        $username = $_POST['user'];
        $password = $_POST['pass'];

        // Validate if username/email is not empty
        if (empty($username)) {
            $error_username = "Enter username!";
        }

        if (empty($password)) {
            $error_password = "Enter password!";
        }

        if (!empty($username) && !empty($password)) {
            // Prepare SQL statement to avoid SQL injection
            $sql = "SELECT * FROM signup WHERE username = ? " ;
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    $login = true;
                    $_SESSION['username'] = $row['username']; // Store username in session
                   
                    $_SESSION['loggedin'] = true;
                    
                    // Store additional user data if needed
                    $_SESSION['user_id'] = $row['id']; // Assuming you have an ID column
                   
                    
                    header("Location: home.php");
                    exit();
                } else {
                    $error_password = "Invalid password! Please try again.";
                }
            } else {
                $error_username = "Invalid username  Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Back</title>
 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="navstyles.css">
    <style>
        
    body {
        background-color: #f8f9fa;
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100vh;
        margin: 0;
        
    }

    header{
        margin-bottom:85px;
    }
    #form {
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
        width: 100%;
        max-width: 500px;
    }
    #form h1 {
        margin-bottom: 20px;
    }
    .btn-primary {
        background-color: #28a745;
        border-color: #28a745;
        width: 100%; /* Ensures button takes full width */
    }
    .btn-primary:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    .btn-google {
        background-color: #ffffff;
        color: #757575;
        border: 1px solid #d6d6d6;
        display: flex;
        align-items: center;
        width: 100%;
        text-align: center;
        padding: 10px;
        margin-top: 10px;
        border-radius: 5px;
    }
    .btn-google img {
        margin-right: 10px;
    }
    .text-center a {
        color: #28a745;
    }
    .text-center a:hover {
        color: #218838;
        text-decoration: underline;
    }
    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
        transition: opacity 1s ease-out; /* Added transition for smooth vanishing */
    }

    /* Media Query for Smaller Screens */
    @media (max-width: 768px) {
        body {
            padding-top: 76px; /* Adjust if needed */
        }
        #form {
            padding: 20px;
            margin-top: 15px;
        }
        #form h1 {
            font-size: 18px;
        }
    }

    @media (max-width: 480px) {
        body {
            padding-top: 96px; /* Adjust if needed */
        }
        #form {
            padding: 15px;
            margin-top: 10px;
        }
        #form h1 {
            font-size: 16px;
        }
    }
    .footer {
            background-color: #333;
            position: absolute;
            color: white;
            text-align: center;
            padding: 10px;
           
            bottom: 0;
            width: 98.7vw;
        }
        .footer a {
            color: #4a90e2;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }#floater {
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

    <div id="form" class="container">
        <h1 class="text-center">Login</h1>
        <?php if (!empty($error_username)): ?>
            <div id="error_username" class="alert alert-danger"><?php echo $error_username; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_password)): ?>
            <div id="error_password" class="alert alert-danger"><?php echo $error_password; ?></div>
        <?php endif; ?>
        <form name="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" onsubmit="return isValid()">
            <div class="mb-3">
                <label for="user" class="form-label"> Enter username</label>
                <input type="text" minlength="12" maxlength="12" value="7155" id="user" name="user" class="form-control">
            </div>
            <div class="mb-3">
                <label for="pass" class="form-label">Password:</label>
                <input type="password" id="pass" name="pass" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Login</button>
        </form>
        <div class="text-center mt-3">
            <span>Don't have an account? </span><a href="signup.php">Sign Up</a>
        </div>
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
    <script>
        function isValid() {
            var user = document.getElementById("user").value.trim();
            var pass = document.getElementById("pass").value.trim();

            if (user === "") {
                showAlert("error_username", "Enter username or email!");
                return false;
            }
            if (pass === "") {
                showAlert("error_password", "Enter password!");
                return false;
            }
            return true;
        }

        function showAlert(elementId, message) {
            var alertElement = document.getElementById(elementId);
            if (!alertElement) {
                alertElement = document.createElement("div");
                alertElement.id = elementId;
                alertElement.className = "alert alert-danger";
                document.getElementById("form").insertBefore(alertElement, document.getElementsByTagName("form")[0]);
            }
            alertElement.innerHTML = message;
            alertElement.style.opacity = 1; // Ensure alert is visible
            setTimeout(function() {
                alertElement.style.opacity = 0; // Fade out alert after 3 seconds
            }, 3000); // 3000 milliseconds = 3 seconds
        }
    </script>
</body>
</html>