<?php if (!empty($images)): ?>
    <div id="hotelCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php foreach ($images as $index => $image): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                    <img src="<?= htmlspecialchars($image) ?>" class="d-block w-100" style="max-height: 300px; object-fit: cover;" alt="<?= htmlspecialchars($hotel['name']) ?>">
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#hotelCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#hotelCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>
<?php else: ?>
    <img src="default-image.jpg" class="d-block w-100" style="max-height: 300px; object-fit: cover;" alt="No Image Available">
<?php endif; ?>