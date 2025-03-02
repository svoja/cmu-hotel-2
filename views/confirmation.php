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
?>

<main class="container mt-4">
    <div class="card shadow-sm p-4">
        <h3 class="fw-bold text-center">Booking Confirmation</h3>
        <p class="text-center text-muted">Your booking has been successfully processed.</p>
        <hr>

        <h5 class="fw-bold">Reservation Details</h5>
        <p><strong>Hotel:</strong> <?= htmlspecialchars($reservation['hotel_name']); ?></p>
        <p><strong>Room Type:</strong> <?= htmlspecialchars($reservation['room_name']); ?></p>
        <p><strong>Check-in:</strong> <?= htmlspecialchars($reservation['check_in']); ?></p>
        <p><strong>Check-out:</strong> <?= htmlspecialchars($reservation['check_out']); ?></p>
        <p><strong>Total Price:</strong> ฿<?= number_format($reservation['total_price'], 2); ?></p>
        
        <h5 class="fw-bold mt-4">Payment Details</h5>
        <p><strong>Payment Method:</strong> <?= htmlspecialchars($reservation['payment_method'] ?: 'Not Provided'); ?></p>
        <p><strong>Payment Status:</strong> <?= ucfirst($reservation['payment_status']); ?></p>
        
        <div class="text-center mt-4">
            <a href="my-bookings.php" class="btn btn-primary">Back to Dashboard</a>
            <a href="confirmation.php?reservation_id=<?= $reservation_id; ?>" class="btn btn-secondary">View Confirmation</a>
        </div>
    </div>
</main>

<?php require_once("../partials/footer.php"); ?>