<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../partials/header.php");
require_once("../partials/nav.php");
require_once("../config/middleware.php");

adminMiddleware();

// Fetch Statistics
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalHotels = $pdo->query("SELECT COUNT(*) FROM hotels")->fetchColumn();
$totalBookings = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$pendingSupport = $pdo->query("SELECT COUNT(*) FROM support_requests WHERE status = 'pending'")->fetchColumn();

// Fetch Recent Activities
$recentHotels = $pdo->query("SELECT name, created_at FROM hotels ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$recentBookings = $pdo->query("SELECT r.id, h.name AS hotel_name, r.check_in, r.check_out, r.status 
                               FROM reservations r 
                               JOIN hotels h ON r.hotel_id = h.id 
                               ORDER BY r.created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
        <div class="row">
            <?php require_once("../partials/sidebar.php"); ?>

            <div class="col-md-8 col-lg-9">
                <div class="card shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-center mb-3">Admin Dashboard</h5>
                        <p class="text-muted text-center">Dashboard for admin</p>
                        <hr class="my-3">

                        <!-- Statistics Overview -->
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="fw-bold">Total Users</h6>
                                    <p class="fs-4"><?= $totalUsers; ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="fw-bold">Total Hotels</h6>
                                    <p class="fs-4"><?= $totalHotels; ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="fw-bold">Total Bookings</h6>
                                    <p class="fs-4"><?= $totalBookings; ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="fw-bold">Pending Support</h6>
                                    <p class="fs-4"><?= $pendingSupport; ?></p>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- Recent Activities -->
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold">Recent Hotels</h6>
                                <ul class="list-group">
                                    <?php foreach ($recentHotels as $hotel) : ?>
                                        <li class="list-group-item">
                                            <?= htmlspecialchars($hotel['name']); ?>
                                            <span class="text-muted small float-end"><?= date("M d, Y", strtotime($hotel['created_at'])); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold">Recent Bookings</h6>
                                <ul class="list-group">
                                    <?php foreach ($recentBookings as $booking) : ?>
                                        <li class="list-group-item">
                                            <?= htmlspecialchars($booking['hotel_name']); ?> 
                                            <span class="badge bg-<?= $booking['status'] === 'confirmed' ? 'success' : 'warning'; ?>">
                                                <?= ucfirst($booking['status']); ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Quick Actions -->
                        <h6 class="fw-bold">Quick Actions</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <a href="manage-support.php" class="btn btn-primary w-100">Manage Support</a>
                            </div>
                            <div class="col-md-4">
                                <a href="add-hotel.php" class="btn btn-primary w-100">Add Hotel</a>
                            </div>
                            <div class="col-md-4">
                                <a href="manage-users.php" class="btn btn-primary w-100">Manage Users</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
</main>

<?php require_once("../partials/footer.php"); ?>