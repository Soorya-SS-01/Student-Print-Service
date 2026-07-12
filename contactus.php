<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - PSG Institute of Technology and Applied Research</title>
    <link rel="stylesheet" href="navstyles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f4f4f4;
        }
        .header {
            background: #ffffff;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: black;
            border-bottom: 1px solid #eaeaea;
        }
        .header img {
            height: 60px;
            width: auto; /* Ensure logo maintains its aspect ratio */
        }
        .header .logo-container {
            display: flex;
            align-items: center;
        }
        .header .logo-text {
            font-size: 18px;
            font-weight: normal;
            color: #333;
            margin-left: 20px;
        }
        .header nav a {
            margin: 0 10px;
            text-decoration: none;
            color: #333;
            font-size: 18px; /* Match the size of the PSG Institute of Technology and Applied Research text */
        }
        .header nav a:hover {
            color: #50a3d9;
        }
        .hero {
            background-color: #ffffff;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-bottom: 1px solid #eaeaea;
            text-align: left;
            flex-grow: 1; /* Make the hero section grow to fill available space */
        }
        .hero .text-content {
            max-width: 45%;
            margin-right: 20px; /* Add space between text and image */
        }
        .hero h1 {
            font-size: 28px;
            margin-bottom: 20px;
        }
        .hero p {
            font-size: 16px;
            margin-bottom: 20px;
            color: #666;
        }
        .hero img {
            width: 45%; /* Adjust the width as needed */
            height: auto; /* Maintain the aspect ratio */
            max-height: 400px; /* Set a maximum height */
            flex-shrink: 0; /* Prevent the image from shrinking */
        }
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
            position: relative;
            bottom: 0;
            width: 100%;
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

<header>
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
    </header>
<div class="hero">
    <div class="text-content">
        <h1>Contact Us</h1>
        <p><strong>PSG Institute of Technology and Applied Research</strong></p>
        <p><strong>Address:</strong> Avinashi Road, Peelamedu, Coimbatore - 641004, Tamil Nadu, India</p>
        <p><strong>Phone:</strong> +91-422-2572177</p>
        <p><strong>Email:</strong> info@psginstitute.edu</p>
        <p>Feel free to reach out to us with any queries or for more information about our printing services. We look forward to hearing from you!</p>
    </div>
    <img src="contaciimage.webp" alt="Contact Image">
</div>

<div class="footer">
    <p>&copy; 2024 PSG Institute of Technology and Applied Research. All rights reserved.</p>
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
