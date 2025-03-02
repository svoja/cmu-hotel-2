<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("../config/db.php");
require_once("../partials/header.php");
require_once("../partials/nav.php");

// Get room type and hotel ID
$room_types_id = isset($_GET['room_types_id']) ? intval($_GET['room_types_id']) : 0;
$hotel_id = isset($_GET['hotel_id']) ? intval($_GET['hotel_id']) : 0;

if ($room_types_id > 0 && $hotel_id > 0) {
    // Fetch room details, including available rooms count and discounted price
    $stmt = $pdo->prepare("
        SELECT rt.*, 
            (SELECT COUNT(*) FROM rooms r WHERE r.room_type_id = rt.id AND r.status = 'available') AS available_rooms,
            COALESCE(
                (SELECT (rt.base_price - (rt.base_price * (d.discount_percentage / 100)))
                 FROM discounts d
                 WHERE d.hotel_id = rt.hotel_id AND d.room_type_id = rt.id AND d.status = 'active' LIMIT 1),
                rt.base_price
            ) AS discounted_price
        FROM room_types rt 
        WHERE rt.id = ?
    ");
    $stmt->execute([$room_types_id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch images for this room type
    $stmt = $pdo->prepare("SELECT image_url FROM room_type_images WHERE room_types_id = ?");
    $stmt->execute([$room_types_id]);
    $room_images = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Fetch amenities for this room type
    $stmt = $pdo->prepare("
        SELECT a.name FROM room_type_amenities rta
        INNER JOIN amenities a ON rta.amenity_id = a.id
        WHERE rta.room_type_id = ?
    ");
    $stmt->execute([$room_types_id]);
    $room_amenities = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<main>
        <h3 class="fw-bold text-center my-5">Booking Details</h3>
        <div class="row g-4">
            <div class="col-md-6">
            <div class="card shadow-sm">
                <!-- Room Image Carousel -->
                <?php if (!empty($room_images)) : ?>
                    <div id="carouselRoomImages" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php foreach ($room_images as $index => $image) : ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : ''; ?>">
                                    <img src="<?= htmlspecialchars($image); ?>" class="d-block w-100" style="height: 250px; object-fit: cover;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselRoomImages" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselRoomImages" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                <?php else: ?>
                    <img src="assets/img/no-image.jpg" class="img-fluid w-100" style="height: 250px; object-fit: cover;">
                <?php endif; ?>

                <div class="card-body">
                    <h5 class="fw-bold"><?= htmlspecialchars($room["name"]); ?></h5>
                    <p>Capacity: <?= $room["capacity"]; ?> guests</p>

                    <!-- Display Discounted Price -->
                    <p class="fw-bold">
                        <?php if ($room['discounted_price'] < $room['base_price']) : ?>
                            <span class="text-danger text-decoration-line-through">
                                ฿<?= number_format($room["base_price"], 2); ?>
                            </span>
                            <span class="text-success">฿<?= number_format($room["discounted_price"], 2); ?></span>
                        <?php else: ?>
                            <span class="text-success">฿<?= number_format($room["base_price"], 2); ?></span>
                        <?php endif; ?>
                    </p>

                    <!-- Room Amenities -->
                    <h6 class="fw-bold mt-3">Amenities</h6>
                    <?php if (!empty($room_amenities)) : ?>
                        <ul class="list-unstyled">
                            <?php foreach ($room_amenities as $amenity) : ?>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> <?= htmlspecialchars($amenity); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">No amenities listed.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
            
            <!-- Booking Form -->
            <div class="col-md-6">
                <div class="card shadow-sm p-4">
                    <h5 class="fw-bold">Enter Booking Details</h5>
                    <form id="bookingForm" action="../controllers/process-booking.php" method="POST">
                        <input type="hidden" name="room_types_id" value="<?= $room_types_id; ?>">
                        <input type="hidden" name="hotel_id" value="<?= $hotel_id; ?>">

                        <!-- Check-in and Check-out -->
                        <div class="mb-3">
                            <label class="form-label">Check-in Date</label>
                            <input type="date" name="check_in" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Check-out Date</label>
                            <input type="date" name="check_out" class="form-control" required>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select id="paymentMethod" name="payment_method" class="form-select" required>
                                <option value="cash">Cash</option>
                                <option value="credit_card">Credit Card</option>
                            </select>
                        </div>

                        <!-- Credit Card Details (Hidden by Default) -->
                        <div id="creditCardDetails" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Cardholder Name</label>
                                <input type="text" name="cardholder_name" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Card Number</label>
                                <input type="text" name="card_number" class="form-control" maxlength="16">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Expiry Date (MM/YY)</label>
                                    <input type="text" name="expiry_date" class="form-control" maxlength="5">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">CVV</label>
                                    <input type="text" name="cvv" class="form-control" maxlength="3">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-3">Confirm Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.getElementById("paymentMethod").addEventListener("change", function () {
    let creditCardSection = document.getElementById("creditCardDetails");
    if (this.value === "credit_card") {
        creditCardSection.style.display = "block";
    } else {
        creditCardSection.style.display = "none";
    }
});
</script>

<?php require_once("../partials/footer.php"); ?>