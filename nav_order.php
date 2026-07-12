<?php
// Ensure session is started on all pages that include this navbar
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
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
            <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <li class="profile-dropdown">
                    <div class="profile-icon" id="profileIcon">
                        <img src="profile.png" alt="Profile" class="profile-image">
                    </div>
                    <div class="dropdown-menu" id="profileDropdown">
                        <div class="username-display">
                            <i class="fas fa-user"></i>
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </div>
                        
                        <a href="logout.php" class="dropdown-item logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </div>
                </li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
            <li><a href="contactus.php">Contact Us</a></li>
        </ul>
    </nav>
</header>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileIcon = document.getElementById('profileIcon');
    const profileDropdown = document.getElementById('profileDropdown');
    
    if (profileIcon && profileDropdown) {
        // Toggle dropdown on profile icon click
        profileIcon.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            profileDropdown.classList.toggle('show');
        });
        
        // Close the dropdown when clicking elsewhere on the page
        document.addEventListener('click', function(event) {
            if (profileDropdown.classList.contains('show') && 
                !profileDropdown.contains(event.target) && 
                !profileIcon.contains(event.target)) {
                profileDropdown.classList.remove('show');
            }
        });
        
        // Prevent dropdown from closing when clicking inside it
        profileDropdown.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
});
</script>