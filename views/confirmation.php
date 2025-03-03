<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("../config/db.php");
require_once("../partials/header.php");
require_once("../partials/nav.php");

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get reservation ID from query parameter
$reservation_id = isset($_GET['reservation_id']) ? intval($_GET['reservation_id']) : 0;
if ($reservation_id <= 0) {
    die("<div class='alert alert-danger'>Invalid reservation.</div>");
}

// Fetch reservation details
$stmt = $pdo->prepare("SELECT r.*, h.name AS hotel_name, rt.name AS room_name, rt.base_price,
        p.payment_method, p.status AS payment_status
    FROM reservations r
    JOIN hotels h ON r.hotel_id = h.id
    JOIN reservation_rooms rr ON r.id = rr.reservation_id
    JOIN rooms ro ON rr.room_id = ro.id
    JOIN room_types rt ON ro.room_type_id = rt.id
    LEFT JOIN payments p ON r.id = p.reservation_id
    WHERE r.id = ? AND r.user_id = ?");
$stmt->execute([$reservation_id, $_SESSION['user_id']]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    die("<div class='alert alert-danger'>Reservation not found.</div>");
}

// Calculate number of nights
$check_in = new DateTime($reservation['check_in']);
$check_out = new DateTime($reservation['check_out']);
$nights = $check_in->diff($check_out)->days;
?>

<div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow rounded-3 overflow-hidden">
                <!-- Success banner -->
                <div class="bg-success text-white p-4 text-center">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h2 class="fw-bold">Booking Confirmed!</h2>
                    <p class="lead mb-0">Your reservation has been successfully processed.</p>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <!-- Reservation ID banner -->
                    <div class="d-flex justify-content-between align-items-center mb-4 bg-light p-3 rounded">
                        <div>
                            <span class="text-muted">Reservation ID</span>
                            <h5 class="fw-bold mb-0">#<?= $reservation_id ?></h5>
                        </div>
                        <div>
                            <span class="badge bg-<?= strtolower($reservation['payment_status']) === 'paid' ? 'success' : 'warning' ?> p-2">
                                <?= ucfirst($reservation['payment_status']) ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Hotel details -->
                    <div class="row">
                        <div class="col-md-7">
                            <h4 class="fw-bold"><?= htmlspecialchars($reservation['hotel_name']); ?></h4>
                            <p class="text-muted">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <?= htmlspecialchars($reservation['room_name']); ?> Room
                            </p>
                        </div>
                        <div class="col-md-5 text-md-end">
                            <div class="mb-2">
                                <span class="badge bg-primary p-2"><?= $nights ?> Night<?= $nights > 1 ? 's' : '' ?></span>
                            </div>
                            <h5 class="fw-bold text-primary">฿<?= number_format($reservation['total_price'], 2); ?></h5>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Check-in/Check-out details -->
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="me-3">
                                    <div class="bg-light p-3 rounded-circle">
                                        <i class="fas fa-calendar-check text-primary"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Check-in</h6>
                                    <h5 class="fw-bold"><?= date('D, M d, Y', strtotime($reservation['check_in'])); ?></h5>
                                    <p class="small text-muted mb-0">After 2:00 PM</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="me-3">
                                    <div class="bg-light p-3 rounded-circle">
                                        <i class="fas fa-calendar-times text-primary"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Check-out</h6>
                                    <h5 class="fw-bold"><?= date('D, M d, Y', strtotime($reservation['check_out'])); ?></h5>
                                    <p class="small text-muted mb-0">Before 12:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Payment details -->
                    <h5 class="fw-bold mb-3">Payment Details</h5>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <span class="text-muted">Payment Method:</span><br>
                                <strong><?= htmlspecialchars($reservation['payment_method'] ?: 'Not Provided'); ?></strong>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <span class="text-muted">Room Price:</span><br>
                                <strong>฿<?= number_format($reservation['base_price'], 2); ?> × <?= $nights ?> night<?= $nights > 1 ? 's' : '' ?></strong>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <span class="text-muted">Payment Status:</span><br>
                                <strong><?= ucfirst($reservation['payment_status']); ?></strong>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <span class="text-muted">Total Amount:</span><br>
                                <strong class="text-primary">฿<?= number_format($reservation['total_price'], 2); ?></strong>
                            </p>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Important Information -->
                    <div class="bg-light p-4 rounded mb-4">
                        <h5 class="fw-bold mb-3">Important Information</h5>
                        <ul class="mb-0">
                            <li>Please present a valid ID and credit card at check-in.</li>
                            <li>Early check-in is subject to availability.</li>
                            <li>Free cancellation until 24 hours before arrival.</li>
                        </ul>
                    </div>
                    
                    <!-- Actions -->
                    <div class="d-flex flex-column flex-md-row gap-2 justify-content-center mt-4">
                        <a href="my-bookings.php" class="btn btn-primary">
                            <i class="fas fa-th-list me-2"></i> My Bookings
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Help section -->
            <div class="text-center mt-4">
                <p class="text-muted">Need help with your booking?</p>
                <a href="support.php" class="btn btn-sm btn-outline-secondary">Contact Support</a>
            </div>
        </div>
    </div>
</div>

<?php require_once("../partials/footer.php"); ?>