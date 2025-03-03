<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}
require_once("../partials/header.php");
require_once("../partials/nav.php");
require_once("../config/db.php");
$hotel_id = isset($_GET['hotel_id']) ? intval($_GET['hotel_id']) : 0;
if ($hotel_id > 0) {
    $stmt = $pdo->prepare("SELECT name, address, city, state, country, zip_code, map_url, description FROM hotels WHERE id = ?");
    $stmt->execute([$hotel_id]);
    $hotel = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch top 3 images for this hotel
    $stmt = $pdo->prepare("SELECT image_url FROM hotel_images WHERE hotel_id = ? LIMIT 3");
    $stmt->execute([$hotel_id]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Fetch hotel amenities
    $stmt = $pdo->prepare("SELECT a.name FROM amenities a INNER JOIN hotel_amenities ha ON a.id = ha.amenity_id WHERE ha.hotel_id = ?");
    $stmt->execute([$hotel_id]);
    $amenities = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Fetch hotel review score and total reviews
    $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(id) as total_reviews FROM reviews WHERE hotel_id = ?");
    $stmt->execute([$hotel_id]);
    $review_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch top 3 good reviews (above 4.5 rating) with user names & review dates
    $stmt = $pdo->prepare("
        SELECT u.name AS reviewer_name, r.review_text, r.rating, r.created_at 
        FROM reviews r 
        INNER JOIN users u ON r.user_id = u.id 
        WHERE r.hotel_id = ? 
        AND r.rating > 4.5 
        ORDER BY r.rating DESC, r.created_at DESC
    ");
    $stmt->execute([$hotel_id]);
    $good_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all reviews for this hotel
    $stmt = $pdo->prepare("
    SELECT u.name AS reviewer_name, r.review_text, r.rating, r.created_at 
    FROM reviews r 
    INNER JOIN users u ON r.user_id = u.id 
    WHERE r.hotel_id = ? 
    ORDER BY r.created_at DESC
    ");
    $stmt->execute([$hotel_id]);
    $all_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
<style>
.rating label {
    cursor: pointer;
    margin-right: 5px;
    transition: transform 0.2s ease-in-out;
}

.rating label:hover {
    transform: scale(1.2);
}
</style>
<main>
    <?php if (!empty($hotel)): ?>
        <div class="card shadow-sm mb-4">
            <!-- Hotel Image Section -->
            <?php include '../partials/hotel-images.php'; ?>

            <!-- Hotel Details with Two Columns -->
            <div class="row g-0">
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title fw-bold"> <?= htmlspecialchars($hotel['name']) ?> </h5>
                        <p class="text-muted">
                            <?= htmlspecialchars($hotel['address']) ?>, 
                            <?= htmlspecialchars($hotel['city']) ?>, 
                            <?= htmlspecialchars($hotel['state']) ?>, 
                            <?= htmlspecialchars($hotel['country']) ?>,
                            <?= htmlspecialchars($hotel['zip_code']) ?>
                            <a href="<?= htmlspecialchars($hotel['map_url']); ?>" target="_blank">SEE MAP</a>
                        </p>
                        <p class="mt-2 pt-2 border-top small"> <?= nl2br(htmlspecialchars($hotel['description'])) ?> </p>

                        <!-- Hotel Facilities Section -->
                        <?php include '../partials/hotel-facilities.php'; ?>
                    </div>
                </div>

                <!-- Reviews Section -->
                <div class="col-md-4 border-start">
                    <?php include '../partials/hotel-reviews.php'; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">Hotel details not found.</div>
    <?php endif; ?>

    <?php include '../partials/room-query.php'; ?>

    <!-- Available Rooms Section -->
    <?php if (!empty($rooms)) : ?>
        <div class="mt-3">
            <h5 class="fw-bold">Available Rooms</h5>
            <div class="row row-cols-1 g-4">
                <?php foreach ($rooms as $room) : ?>
                    <?php include '../partials/room-card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else : ?>
        <div class="alert alert-danger mt-3">No rooms available for this hotel.</div>
    <?php endif; ?>

<!-- Review Section -->
<div class="mt-5">
    <div class="card shadow-sm bg-white">
        <div class="card-body">
            <h5 class="fw-bold text-dark">Customer Reviews</h5>

            <!-- Average Rating -->
            <div class="mb-3">
                <div class="d-flex align-items-center">
                    <?php 
                    $avg_rating = round($review_stats['avg_rating'], 1);
                    $rating_text = ($avg_rating >= 4.5) ? "Exceptional" :
                                (($avg_rating >= 4.0) ? "Great" :
                                (($avg_rating >= 3.0) ? "Good" : "Average"));
                    ?>
                    <div class="bg-light text-dark px-3 py-2 rounded-3 fw-bold border">
                        <?= number_format($avg_rating, 1) ?> / 5.0 <span class="text-muted">- <?= $rating_text ?></span>
                    </div>
                    <span class="ms-3 fs-6 text-muted">(<?= $review_stats['total_reviews']; ?> reviews)</span>
                </div>
            </div>

            <!-- Display Existing Reviews -->
            <div class="reviews-container">
                <?php if ($review_stats['total_reviews'] > 0): ?>
                    <?php if (!empty($all_reviews)): ?>
                        <?php foreach ($all_reviews as $review): ?>
                            <div class="review-card bg-light border rounded-3 p-3 mb-3">
                                <strong class="text-dark"><?= htmlspecialchars($review['reviewer_name']) ?></strong> 
                                <span class="text-muted">- <?= number_format($review['rating'], 1) ?> / 5.0</span>
                                <br>
                                <small class="text-muted fst-italic">
                                    <?= date("F j, Y", strtotime($review['created_at'])) ?> <!-- ✅ Proper Date Formatting -->
                                </small>
                                <br>
                                <p class="text-dark m-0">
                                    "<?= htmlspecialchars($review['review_text']) ?>"
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No reviews available.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted">No reviews yet.</p>
                <?php endif; ?>
            </div>

            <!-- Review Form -->
            <?php if (isset($_SESSION['user_id'])) : ?>
                <div class="card bg-white border shadow-sm mt-4 rounded-3">
                    <div class="card-body">
                        <h5 class="fw-bold text-dark">Leave a Review</h5>
                        <form action="../hotel/submit-review.php" method="POST">
                            <input type="hidden" name="hotel_id" value="<?= $hotel_id; ?>">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Your Rating (1-5)</label>
                                <select name="rating" class="form-select bg-light text-dark border">
                                    <option value="5">5 - Excellent</option>
                                    <option value="4">4 - Great</option>
                                    <option value="3">3 - Good</option>
                                    <option value="2">2 - Fair</option>
                                    <option value="1">1 - Poor</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Your Review</label>
                                <textarea name="review_text" class="form-control bg-light text-dark border rounded-3" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-dark w-100">Submit Review</button>
                        </form>
                    </div>
                </div>
            <?php else : ?>
                <p class="text-muted">You must be logged in to leave a review.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</main>

<?php require_once("../partials/footer.php"); ?>