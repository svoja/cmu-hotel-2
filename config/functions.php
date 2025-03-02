<?php
function showBookings($status, $bookings) {
    $filteredBookings = array_filter($bookings, fn($b) => $b['status'] === $status);

    if (empty($filteredBookings)) {
        echo "<p class='text-center text-muted'>No bookings found in this category.</p>";
        return;
    }

    echo "<div class='row row-cols-1 row-cols-md-2 g-3'>";
    foreach ($filteredBookings as $booking) : ?>
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold"><?= htmlspecialchars($booking['hotel_name']); ?></h6>
                    <p class="text-muted small mb-1">
                        Check-in: <?= htmlspecialchars($booking['check_in']); ?> | Check-out: <?= htmlspecialchars($booking['check_out']); ?>
                    </p>
                    <p class="text-muted small">Total Price: ฿<?= number_format($booking['total_price'], 2); ?></p>
                    <span class="badge bg-<?= $status === 'pending' ? 'warning' : ($status === 'confirmed' ? 'success' : 'danger'); ?>">
                        <?= ucfirst($status); ?>
                    </span>
                    <div class="mt-2">
                        <a href="confirmation.php?reservation_id=<?= $booking['id']; ?>" class="btn btn-primary btn-sm">View</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;
    echo "</div>";
}
?>