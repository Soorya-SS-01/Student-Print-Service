<?php
// This should be a PHP file (navbar1.php) rather than HTML to handle the session check
// Check if a session is already active before starting a new one
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// If not logged in and not already on the login page, redirect
if (!$isLoggedIn && !strpos($_SERVER['PHP_SELF'], 'admin_login.php')) {
    header("Location: admin_login.php");
    exit;
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="admin_dashboard.php">Print Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if ($isLoggedIn): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'admin_dashboard.php') !== false) ? 'active' : ''; ?>" href="admin_dashboard.php">Dashboard</a>
                </li>
               <!--  <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'accepted_orders.php') !== false) ? 'active' : ''; ?>" href="admin_accept.php">Accepted Orders</a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'rejected_orders.php') !== false) ? 'active' : ''; ?>" href="admin_reject.php">Rejected Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'daily_collection.php') !== false) ? 'active' : ''; ?>" href="daily_collection.php">Daily Collection</a>
                </li>
                <?php endif; ?>
            </ul>
            <?php if ($isLoggedIn): ?>
            <div class="d-flex">
                <span class="navbar-text me-3">
                    Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>
                </span>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
            <?php else: ?>
            <div class="d-flex">
                <a href="admin_login.php" class="btn btn-outline-light">Login</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</nav>