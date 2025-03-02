<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg my-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/index.php">Nova</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
                       href="/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/views/destinations.php">Destinations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/views/deals.php">Deals</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/views/support.php">Support</a>
                </li>

                <?php if (isset($_SESSION['user_id'])) : ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> 
                            <?= htmlspecialchars($_SESSION['user_name']); ?> 
                            <small class="text-muted">(<?= ucfirst($_SESSION['user_role']); ?>)</small>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/views/my-bookings.php">My Bookings</a></li>

                            <?php if ($_SESSION['user_role'] === 'hotel_owner') : ?>
                                <li><a class="dropdown-item" href="/views/manage-hotel.php">Manage Hotel</a></li>
                            <?php elseif ($_SESSION['user_role'] === 'admin') : ?>
                                <li><a class="dropdown-item" href="/views/admin-dashboard.php">Admin Dashboard</a></li>
                            <?php endif; ?>

                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/auth/logout-process.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else : ?>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>" 
                           href="/views/login.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>