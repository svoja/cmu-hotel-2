<?php require_once("partials/header.php"); ?>
<?php
// Fetch all hotels with their images
$stmt = $pdo->query("
    SELECT 
        h.*, GROUP_CONCAT(hi.image_path) as image_paths
    FROM hotels h 
    LEFT JOIN hotel_images hi ON h.id = hi.hotel_id 
    LEFT JOIN reviews r on h.id = r.hotel_id
    GROUP BY h.id
");
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($hotels as $hotel) { ?>
            <div class="col">
                <div class="card h-100 shadow-sm hover-shadow">
                    <?php 
                    $imagePaths = explode(',', $hotel['image_paths']);
                    $primaryImagePath = !empty($imagePaths[0]) ? $imagePaths[0] : null;
                    
                    if ($primaryImagePath) {
                        $imageUrl = getHotelImageUrl($hotel['name'], imagePath: $primaryImagePath);
                        echo '<img src="' . htmlspecialchars($imageUrl) . '" 
                                   class="card-img-top" 
                                   alt="' . htmlspecialchars($hotel['name']) . '"
                                   style="height: 200px; object-fit: cover;">';
                    }
                    ?>
                    
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title">
                                <a href="hotel_details.php?hotel_id=<?php echo htmlspecialchars($hotel['id']); ?>" 
                                class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($hotel['name']); ?>
                                </a>
                            </h5>
                            <div class="rating-badge">
                                <span class="badge bg-success">
                                    <?php 
                                    $rating = number_format($hotel['average_rating'], 1);
                                    echo $rating . ' / 5.0';
                                    ?>
                                    <i class="bi bi-star-fill ms-1"></i>
                                </span>
                                <div class="text-muted small">
                                    <?php echo $hotel['review_count']; ?>
                                </div>
                            </div>
                        </div>
                        
                        <p class="card-text text-muted mb-3">
                            <i class="bi bi-geo-alt-fill me-1"></i>
                            <?php echo htmlspecialchars($hotel['province']); ?>, 
                            <?php echo htmlspecialchars($hotel['country']); ?>
                        </p>
                        
                        <a href="<?php echo htmlspecialchars($hotel['map_link']); ?>" 
                           target="_blank" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-map me-1"></i> View on Map
                        </a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php require_once("partials/footer.php"); ?>