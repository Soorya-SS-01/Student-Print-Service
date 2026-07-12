<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $servername = "localhost";
    $username = "root";  // Replace with your database username
    $password = "";      // Replace with your database password
    $dbname = "mydatabase";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get form data
    $copies = isset($_POST['copies']) ? intval($_POST['copies']) : 1;
    $pages = isset($_POST['pages']) ? $_POST['pages'] : 'all';
    $custom_pages = isset($_POST['custom_pages']) ? $_POST['custom_pages'] : '';
    $color = isset($_POST['color']) ? 1 : 0;
    $orientation = isset($_POST['orientation']) ? $_POST['orientation'] : 'portrait';
    $sides = isset($_POST['sides']) ? $_POST['sides'] : 'one-sided';
    $paper_type = isset($_POST['paper_type']) ? $_POST['paper_type'] : 'A4';
    $message = isset($_POST['message']) ? $_POST['message'] : '';
    $user_id = $_SESSION['user_id'] ?? 0; // Assuming user_id is stored in session
    $username = $_SESSION['username'] ?? 'Guest';
    $status = 'pending'; // Default status for new orders
    $uploaded_files = []; // Array to store uploaded file names

    // Handle file uploads
    if (isset($_FILES['files'])) {
        $upload_dir = "uploads/";
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Process each uploaded file
        foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['files']['name'][$key];
            $file_size = $_FILES['files']['size'][$key];
            $file_tmp = $_FILES['files']['tmp_name'][$key];
            $file_type = $_FILES['files']['type'][$key];
            
            // Validate file is a PDF
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if ($file_ext != "pdf") {
                $_SESSION['error'] = "Only PDF files are allowed.";
                header("Location: error.php");
                exit();
            }
            
            // Generate unique filename to prevent overwriting
            $unique_file_name = time() . '_' . uniqid() . '_' . $file_name;
            $file_path = $upload_dir . $unique_file_name;
            
            // Move uploaded file to destination
            if (move_uploaded_file($file_tmp, $file_path)) {
                $uploaded_files[] = $unique_file_name;
            } else {
                $_SESSION['error'] = "Failed to upload file: " . $file_name;
                header("Location: error.php");
                exit();
            }
        }
    }

    // Convert uploaded files array to JSON string
    $files_json = json_encode($uploaded_files);
    
    // Prepare SQL statement to insert data
    $stmt = $conn->prepare("INSERT INTO combined_form (user_id, username, copies, pages, custom_pages, color, orientation, sides, paper_type, message, files, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    
    $stmt->bind_param("isisssisssss", $user_id, $username, $copies, $pages, $custom_pages, $color, $orientation, $sides, $paper_type, $message, $files_json, $status);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Get the inserted order ID
        $order_id = $conn->insert_id;
        
        // Store form data in session for use in student.php
        $_SESSION['form_data'] = [
            'order_id' => $order_id,
            'copies' => $copies,
            'pages' => $pages,
            'custom_pages' => $custom_pages,
            'color' => $color,
            'orientation' => $orientation,
            'sides' => $sides,
            'paper_type' => $paper_type,
            'message' => $message,
            'files' => $uploaded_files
        ];
        
        // Redirect to student dashboard
        header("Location: student.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        header("Location: home.php");
        exit();
    }
    
    // Close statement and connection
    $stmt->close();
    $conn->close();
} /* else {
    // If not POST request, redirect to the form page
    header("Location: combined_form.html");
    exit();
} */
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Printer Form</title>
    <link rel="stylesheet" href="navstyles.css">
    <style>
      /* Reset default margin and padding */
      body,
      html {
        margin: 0;
        padding: 0;
        height: 100%; /* Ensures the body takes up the full height of the viewport */
      }

      /* Main content styling */
      .content {
        padding-top: 80px; /* Adjusted to accommodate fixed navbar */
        background-color: #f4f4f4;
        flex: 1; /* Allows the content to take up available space */
      }

      .container {
        max-width: 600px;
        margin: 20px auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      }

      label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
      }

      input[type="text"],
      input[type="number"],
      textarea,
      select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        margin-bottom: 20px;
        border: 1px solid #dddddd;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
      }

      input[type="file"] {
        margin-bottom: 5px;
      }

      input[type="submit"] {
        background-color: #007bff;
        color: #ffffff;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
        margin-top: 10px;
        transition: background-color 0.3s ease;
      }

      input[type="submit"]:hover {
        background-color: #0056b3;
      }

      /* Error message style */
      .error-message {
        color: red;
        font-size: 14px;
        margin-top: 5px;
      }

      .more-settings {
        cursor: pointer;
        color: #000;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 0;
        border-top: 1px solid #ddd;
      }

      .more-settings-content {
        display: none;
        padding: 10px 0;
        border-top: 1px solid #ddd;
      }

      /* Two-column layout */
      .form-column {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
      }

      .form-column .item {
        width: calc(50% - 10px);
      }


      /* Flexbox container for full height layout */
      .flex-container {
        display: flex;
        flex-direction: column;
        min-height: 100vh; /* Ensures the container takes up the full height of the viewport */
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
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
            position: sticky;
            bottom: 0;
            width: 98.7vw;
        }
        .footer a {
            color: #4a90e2;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
  </head>
  <body>
  <?php include('navbar.php'); ?>
    <div class="flex-container">
      <!-- <header>
        <div class="logo-title">
          <img src="logo.jpeg" alt="PSG Logo" class="logo">
          <h1>PSG Institute of Technology and Applied Research</h1>
        </div>
        <nav>
          <ul>
            <li><a href="home.php">Dashboard</a></li>
            <li><a href="order.php">My Order</a></li>
            <li><a href="history.php">History</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="contactus.php">Contact Us</a></li>
          </ul>
        </nav>
      </header> -->
      
      <!-- Main content -->
      <div class="content">
        <div class="container">
          <h1>Document Details </h1>
          <form action="combined_form.php" method="POST" enctype="multipart/form-data">
            <!-- Number of Copies and Print Pages -->
            <div class="form-column">
              <div class="item">
                <label for="copies">Number of Copies:</label>
                <input type="number" id="copies" name="copies" value="1" min="1" required />
              </div>
              <div class="item">
                <label for="pages">Print Pages:</label>
                <select id="pages" name="pages" onchange="showCustomPages()" required>
                  <option value="all">All Pages</option>
                  <option value="even">Even Pages Only</option>
                  <option value="odd">Odd Pages Only</option>
                  <!-- <option value="custom">Custom Pages</option> -->
                </select>
              </div>
            </div>

            <!-- Custom Pages Input -->
            <!-- <div id="specified-pages" style="display: none">
              <label for="custom_pages">Custom Pages:</label>
              <input type="text" id="custom_pages" name="custom_pages" />
            </div> -->

            <!-- File upload field -->
            <label for="files">Upload Files (ONLY PDF):</label>
            <input type="file" id="files" name="files[]" multiple required accept=".pdf" />
            <div class="error-message" id="files-error" style="display: none">
              Please select only PDF files.
            </div>

            <!-- More settings -->
            <div class="more-settings" onclick="toggleMoreSettings()">
              More settings
              <span id="arrow">▼</span>
            </div>

            <div class="more-settings-content" id="more-settings-content" style="display: none">
              <!-- Additional settings -->
              <label for="color">Color:</label>
              <input type="checkbox" id="color" name="color" />

              <label for="orientation">Orientation:</label>
              <select id="orientation" name="orientation" required>
                <option value="portrait">Portrait</option>
                <option value="landscape">Landscape</option>
              </select>

              <label for="sides">Sides:</label>
              <select id="sides" name="sides" required>
                <option value="one-sided">One-Sided</option>
                <option value="two-sided">Two-Sided</option>
              </select>

              <label for="paper_type">Paper Type:</label>
              <select id="paper_type" name="paper_type" required>
                <option value="A4">A4</option>
                <option value="A3">A3</option>
                <option value="record_sheet">Record Sheet</option>
              </select>

            
            </div>

            <input type="submit" value="Submit" />
          </form>
        </div>
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
      function showCustomPages() {
        var printPagesSelect = document.getElementById("pages");
        var customPagesDiv = document.getElementById("specified-pages");

        if (printPagesSelect.value === "custom") {
          customPagesDiv.style.display = "block";
        } else {
          customPagesDiv.style.display = "none";
        }
      }

      function toggleMoreSettings() {
        var moreSettingsContent = document.getElementById("more-settings-content");
        var arrow = document.getElementById("arrow");

        if (moreSettingsContent.style.display === "none" || moreSettingsContent.style.display === "") {
          moreSettingsContent.style.display = "block";
          arrow.innerHTML = "▲";
        } else {
          moreSettingsContent.style.display = "none";
          arrow.innerHTML = "▼";
        }
      }

      // Validate file types
      var filesInput = document.getElementById("files");
      var filesError = document.getElementById("files-error");

      filesInput.addEventListener("change", function () {
        var validExtensions = ["pdf"];
        var files = filesInput.files;
        for (var i = 0; i < files.length; i++) {
          var extension = files[i].name.split(".").pop().toLowerCase();
          if (!validExtensions.includes(extension)) {
            filesError.style.display = "block";
            return;
          }
        }
        filesError.style.display = "none";
      });

      // Adjust body padding-top to accommodate fixed navbar
      var navbarHeight = document.querySelector(".navbar").offsetHeight;
      document.body.style.paddingTop = navbarHeight + "px";
    </script>
  </body>
</html>
