<?php
require_once("../partials/header.php"); // ✅ Header
require_once("../config/db.php"); // ✅ Database connection

// Dummy data (Replace with database query)
$bookings = [
    ['id' => 1, 'hotel' => 'Grand Hotel', 'status' => 'pending', 'date' => '2025-03-10'],
    ['id' => 2, 'hotel' => 'Seaside Resort', 'status' => 'successful', 'date' => '2025-02-28'],
    ['id' => 3, 'hotel' => 'Luxury Inn', 'status' => 'cancelled', 'date' => '2025-02-25'],
];

// Count bookings
$pendingCount = count(array_filter($bookings, fn($b) => $b['status'] === 'pending'));
$successfulCount = count(array_filter($bookings, fn($b) => $b['status'] === 'successful'));
$cancelledCount = count(array_filter($bookings, fn($b) => $b['status'] === 'cancelled'));
?>

<?php require_once __DIR__ . "/../partials/nav.php"; ?>

<main>
        <div class="row">
            <?php require_once("../partials/sidebar.php"); ?>

            <!-- ✅ Booking Details as a Styled Card -->
            <div class="col-md-8 col-lg-9">
                <div class="card shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-center mb-3">Booking Details</h5>
                        <p class="text-muted text-center">Your past and upcoming bookings will appear here.</p>
                        <hr class="my-3">
                        <?php require_once("../partials/booking-tabs.php"); ?>
                    </div>
                </div>
            </div>
        </div>
</main>

<?php require_once("../partials/footer.php"); ?>