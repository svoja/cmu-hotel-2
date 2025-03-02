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
}

?>

<main>
    <?php if (!empty($hotel)): ?>
        <div class="card shadow-sm mt-3">
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

<?php require_once("../partials/footer.php"); ?>