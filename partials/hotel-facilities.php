<?php if (!empty($amenities)): ?>
    <div class="card-body border-top mt-3">
        <h6 class="fw-bold">Facilities</h6>
        <div class="row g-0">
            <?php foreach ($amenities as $amenity): ?>
                <div class="col-6 col-md-4 d-flex align-items-center py-1">
                    <i class="bi bi-check-lg me-2 text-success"></i>
                    <span><?= htmlspecialchars($amenity) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>