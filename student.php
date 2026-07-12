<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

// Fetch form data from session
$formData = $_SESSION['form_data'] ?? null;
$files = isset($formData['files']) ? $formData['files'] : [];

// Initialize default values
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$printPagesOption = 'all'; // Default print pages option
$pageType = 'N/A'; // Default page type

// Process form data if available
if ($formData) {
    $printPagesOption = isset($formData['pages']) ? htmlspecialchars($formData['pages']) : 'all';

    // Determine the page type string based on the selected option
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
            $pageType = 'Custom pages - ' . htmlspecialchars($formData['custom_pages']);
            break;
        default:
            $pageType = 'N/A';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
        /* Your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .total-cost {
            text-align: center;
            margin-top: 20px;
        }

        .total-cost h2 {
            margin-bottom: 10px;
        }

        .total-cost p {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }

        .proceed-btn {
            text-align: center;
        }

        .proceed-btn button {
            padding: 12px 24px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .proceed-btn button:hover {
            background-color: #0056b3;
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?></h1>
        <table>
            <tr>
                <th>Color</th>
                <td><?php echo isset($formData["color"]) ? ($formData["color"] ? 'Yes' : 'No') : 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Orientation</th>
                <td><?php echo isset($formData["orientation"]) ? htmlspecialchars($formData["orientation"]) : 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Copies</th>
                <td><?php echo isset($formData["copies"]) ? htmlspecialchars($formData["copies"]) : 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Sides</th>
                <td><?php echo isset($formData["sides"]) ? htmlspecialchars($formData["sides"]) : 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Paper Type</th>
                <td><?php echo isset($formData["paper_type"]) ? htmlspecialchars($formData["paper_type"]) : 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Page Type</th>
                <td><?php echo htmlspecialchars($pageType); ?></td>
            </tr>
            <tr>
                <th>Pages</th>
                <td id="pageCount">Calculating...</td>
            </tr>
            <!-- New row for files -->
            <tr>
                <th>Files</th>
                <td>
                    <?php
                    foreach ($files as $file) {
                        $filePath = 'uploads/' . $file; // Adjust path to match where files are stored
                        echo "<a href=\"$filePath\" download>$file</a><br>";
                    }
                    ?>
                </td>
            </tr>
        </table>

        <div class="total-cost">
            <h2>Total Cost</h2>
            <p id="totalCost">Calculating...</p>
        </div>

        <div class="proceed-btn">
            <form id="paymentForm" action="payment.php" method="POST">
                <input type="hidden" name="formData" id="formData" value="">
                <input type="hidden" name="pageOption" id="pageOption" value="">
                <input type="hidden" name="totalCost" id="totalCostInput" value="">
                <input type="hidden" name="totalPages" id="totalPagesInput" value="">
                <button type="submit" id="submitBtn" disabled>Proceed to Payment</button>
            </form>
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
        // Function to get page count of a PDF file
async function getPageCount(filePath) {
    const loadingTask = pdfjsLib.getDocument(filePath);
    const pdf = await loadingTask.promise;
    return pdf.numPages;
}

// Function to get the total number of pages based on custom page range
function getCustomPageCount(customPages) {
    let totalPageCount = 0;
    const ranges = customPages.split(',');
    ranges.forEach(range => {
        const parts = range.split('-').map(Number);
        if (parts.length === 1) {
            totalPageCount++; // Single page specified
        } else if (parts.length === 2) {
            const start = parts[0];
            const end = parts[1];
            if (!isNaN(start) && !isNaN(end) && start <= end) {
                totalPageCount += (end - start + 1);
            }
        }
    });
    return totalPageCount;
}

// Function to calculate total page count for selected files and page option
async function calculateTotalPageCount(files, pageOption, customPages = '') {
    let totalPageCount = 0;
    for (let i = 0; i < files.length; i++) {
        try {
            const filePageCount = await getPageCount('uploads/' + files[i]); // Adjust path as needed
            if (pageOption === 'custom') {
                totalPageCount += getCustomPageCount(customPages);
            } else if (pageOption === 'odd') {
                totalPageCount += Math.ceil(filePageCount / 2);
            } else if (pageOption === 'even') {
                totalPageCount += Math.floor(filePageCount / 2);
            } else { // 'all' pages
                totalPageCount += filePageCount;
            }
        } catch (error) {
            console.error('Error loading PDF:', error.message);
            // Handle error, show error message or fallback behavior
        }
    }
    return totalPageCount;
}

// Function to calculate total cost based on page count
function calculateTotalCost(pageCount, copies, paperType, isColor) {
    let costPerPage =0;
    if (isColor){
        if (paperType ==='A3') {costPerPage = 30;}
        else{ costPerPage = 10;}
    }
    else{
        if(paperType == 'A4') {costPerPage =2;}
        else if( paperType == 'record_sheet'){ costPerPage =2.5;}
        else {costPerPage = 10;}
    }
     // Adjust cost based on paper type and color option
    return pageCount * copies * costPerPage;
}

// Main script
document.addEventListener('DOMContentLoaded', async function() {
    const files = <?php echo json_encode($files); ?>;
    const copies = <?php echo isset($formData["copies"]) ? intval($formData["copies"]) : 1; ?>;
    const paperType = <?php echo json_encode($formData["paper_type"] ?? 'A4'); ?>;
    const pageOption = <?php echo json_encode($printPagesOption); ?>; // Get user-selected print pages option
    const customPages = <?php echo json_encode($formData['custom_pages'] ?? ''); ?>; // Custom pages
    const isColor = <?php echo json_encode(isset($formData["color"]) ? $formData["color"] : false); ?>; // Color option

    try {
        let pageCount = await calculateTotalPageCount(files, pageOption, customPages);
        let totalCost = calculateTotalCost(pageCount, copies, paperType, isColor);
console.log(pageCount,totalCost);

        document.getElementById('pageCount').innerText = pageCount;
        document.getElementById('totalCost').innerText = 'Rs.' + totalCost.toFixed(2);

        // Update form data with calculated values
        document.getElementById('formData').value = JSON.stringify(<?php echo json_encode($formData); ?>);
        document.getElementById('pageOption').value = pageOption;
        document.getElementById('totalCostInput').value = totalCost;
        document.getElementById('totalPagesInput').value = pageCount;

        // Enable submit button once calculations are done
        document.getElementById('submitBtn').removeAttribute('disabled');
    } catch (error) {
        console.error('Error calculating total page count and cost:', error.message);
        document.getElementById('pageCount').innerText = 'Error';
        document.getElementById('totalCost').innerText = 'Error';
        // Optionally handle error display or fallback behavior
    }
});
</script>
</body>
</html>