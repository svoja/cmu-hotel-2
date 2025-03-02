<div class="row row-cols-1 row-cols-md-2 g-3">
    <?php foreach ($bookings as $booking) : ?>
        <?php if ($booking['status'] === str_replace("#", "", $_SERVER["REQUEST_URI"])) : ?>
            <div class="col">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="fw-bold"><?= htmlspecialchars($booking['hotel']); ?></h6>
                        <p class="text-muted small mb-1">Date: <?= $booking['date']; ?></p>
                        <span class="badge bg-<?= $booking['status'] === 'pending' ? 'warning' : ($booking['status'] === 'successful' ? 'success' : 'danger'); ?>">
                            <?= ucfirst($booking['status']); ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>