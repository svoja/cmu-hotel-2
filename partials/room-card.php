<div class="col">
    <div class="row g-3">
        <!-- Left Column: Room Images -->
        <div class="col-md-4 d-flex align-items-stretch">
            <div class="rounded overflow-hidden w-100 h-100">
                <?php if (!empty($room_images[$room['id']])) : ?>
                    <div id="carouselRoom<?= $room['id']; ?>" class="carousel slide h-100" data-bs-ride="carousel">
                        <div class="carousel-inner h-100">
                            <?php foreach ($room_images[$room['id']] as $index => $img) : ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : ''; ?> h-100">
                                    <img src="<?= htmlspecialchars($img); ?>" class="d-block w-100 h-100"
                                         style="object-fit: cover; border-radius: 10px;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselRoom<?= $room['id']; ?>" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselRoom<?= $room['id']; ?>" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                <?php else: ?>
                    <img src="assets/img/no-image.jpg" class="img-fluid w-100 h-100"
                         style="object-fit: cover; border-radius: 10px;">
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Right Side: Room Details -->
        <div class="col-md-8">
            <div class="card h-100 shadow-sm">
                <div class="row g-0 h-100">
                    <!-- Middle Column: Amenities -->
                    <div class="col-md-6 border-end">
                        <div class="card-body">
                            <h6 class="fw-bold">Amenities</h6>
                            <?php if (!empty($room_amenities[$room['id']])) : ?>
                                <ul class="list-unstyled">
                                    <?php foreach ($room_amenities[$room['id']] as $amenity) : ?>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> <?= htmlspecialchars($amenity); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted">No amenities listed.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right Column: Room Details -->
                    <div class="col-md-6">
                        <div class="card-body">
                            <h6 class="card-title fw-bold"><?= htmlspecialchars($room["name"]); ?></h6>
                            <p class="small">Capacity: <?= $room["capacity"]; ?> guests</p>

                            <!-- Show discounted price if available -->
                            <?php if ($room['discounted_price'] < $room['base_price']) : ?>
                                <p class="fw-bold">
                                    <span class="text-danger text-decoration-line-through">
                                        ฿<?= number_format($room["base_price"], 2); ?>
                                    </span>
                                    <span class="text-success">
                                        ฿<?= number_format($room["discounted_price"], 2); ?>
                                    </span>
                                </p>
                            <?php else: ?>
                                <p class="fw-bold text-success">฿<?= number_format($room["base_price"], 2); ?></p>
                            <?php endif; ?>

                            <p class="text-muted small">Rooms Available: <?= $room['available_rooms']; ?></p>

                            <?php if ($room['available_rooms'] > 0): ?>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="booking.php?room_types_id=<?= $room['id']; ?>&hotel_id=<?= $hotel_id; ?>" class="btn btn-primary btn-sm">
                                        Book now
                                    </a>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-warning btn-sm">
                                        Login to Book
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="text-danger fw-bold">No rooms left</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>