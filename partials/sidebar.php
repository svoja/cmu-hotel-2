<div class="col-md-4 col-lg-3">
    <div class="card shadow-sm p-3 rounded-3">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="../views/my-bookings.php">My Bookings</a></li>
            <li class="nav-item"><a class="nav-link" href="../views/support.php">Support</a></li>
            <li class="nav-item"><a class="nav-link" href="../views/profile.php">Profile</a></li>

            <!-- Role-Specific Menu Items -->
            <?php if ($_SESSION['user_role'] === 'admin') : ?>
                <li class="nav-item"><a class="nav-link text-primary" href="../admin/dashboard.php">Admin Dashboard</a></li>
                <li class="nav-item"><a class="nav-link text-primary" href="../admin/manage-users.php">Manage Users</a></li>
                <li class="nav-item"><a class="nav-link text-primary" href="../admin/manage-support.php">Manage Support</a></li>
                <li class="nav-item"><a class="nav-link text-primary" href="../admin/add-hotel.php">Add Hotel</a></li>
            <?php elseif ($_SESSION['user_role'] === 'hotel_owner') : ?>
                <li class="nav-item"><a class="nav-link text-primary" href="../hotel/manage-hotel.php">Manage Hotel</a></li>
                <li class="nav-item"><a class="nav-link text-primary" href="../hotel/add-room.php">Add Room</a></li>
                <li class="nav-item"><a class="nav-link text-primary" href="../hotel/manage-bookings.php">Manage Bookings</a></li>
            <?php endif; ?>

            <li class="nav-item"><a class="nav-link text-danger" href="../auth/logout-process.php">Logout</a></li>
        </ul>
    </div>
</div>