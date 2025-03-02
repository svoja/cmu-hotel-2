<?php
require_once("../partials/header.php");
require_once("../config/db.php");
require_once("../config/functions.php"); // ✅ Include the function once

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch all bookings for the logged-in user
$stmt = $pdo->prepare("
    SELECT r.id, r.check_in, r.check_out, r.total_price, r.status, h.name AS hotel_name
    FROM reservations r
    JOIN hotels h ON r.hotel_id = h.id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once("../partials/nav.php");
?>

<main>
    <div class="row">
        <?php require_once("../partials/sidebar.php"); ?>

        <!-- Booking Tabs -->
        <div class="col-md-8 col-lg-9">
            <div class="card shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-center mb-3">My Bookings</h5>
                    <p class="text-muted text-center">View and manage your past and upcoming bookings.</p>
                    <hr class="my-3">

                    <!-- Tabs -->
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#upcoming">Upcoming</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#completed">Completed</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cancelled">Cancelled</button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="upcoming">
                            <?php showBookings('pending', $bookings); ?>
                        </div>
                        <div class="tab-pane fade" id="completed">
                            <?php showBookings('confirmed', $bookings); ?>
                        </div>
                        <div class="tab-pane fade" id="cancelled">
                            <?php showBookings('cancelled', $bookings); ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once("../partials/footer.php"); ?>
