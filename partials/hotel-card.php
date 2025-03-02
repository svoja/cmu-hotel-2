<?php
$hotel_id = isset($hotel['id']) ? $hotel['id'] : (int)($_GET['hotel_id'] ?? 0);

if (!$hotel_id) {
    echo "<div class='alert alert-danger'>Invalid hotel ID.</div>";
    return;
}

try {
    // Fetch hotel details with a more optimized query
    $sql = "
    SELECT 
        h.*,
        COALESCE(
            (SELECT image_url FROM hotel_images WHERE hotel_id = h.id AND is_primary = 1 ORDER BY id ASC LIMIT 1),
            (SELECT image_url FROM hotel_images WHERE hotel_id = h.id ORDER BY id ASC LIMIT 1)
        ) AS image_url,
        COALESCE((SELECT AVG(rating) FROM reviews WHERE hotel_id = h.id), 0) AS avg_rating,
    
        -- ✅ Fetch lowest price from only available rooms
        COALESCE(
            (SELECT MIN(rt.base_price) 
             FROM room_types rt 
             INNER JOIN rooms r ON rt.id = r.room_type_id 
             WHERE r.hotel_id = h.id AND r.status = 'available'), 
            0
        ) AS lowest_price,
    
        -- ✅ Fetch discounted price only for available rooms
        COALESCE(
            (SELECT MIN(rt.base_price - (rt.base_price * (d.discount_percentage / 100))) 
             FROM room_types rt 
             INNER JOIN rooms r ON rt.id = r.room_type_id 
             LEFT JOIN discounts d ON d.hotel_id = h.id AND d.room_type_id = rt.id AND d.status = 'active'
             WHERE r.hotel_id = h.id AND r.status = 'available'),
            (SELECT MIN(rt.base_price) 
             FROM room_types rt 
             INNER JOIN rooms r ON rt.id = r.room_type_id 
             WHERE r.hotel_id = h.id AND r.status = 'available')
        ) AS discounted_price
    
    FROM hotels h
    WHERE h.id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$hotel_id]);
    $hotel = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$hotel) {
        echo "<div class='alert alert-danger'>Hotel not found.</div>";
        return;
    }
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    echo "<div class='alert alert-danger'>An error occurred while retrieving hotel information.</div>";
    return;
}
?>

<div class="col">
    <div class="card h-100 shadow-sm position-relative">
        <img src="<?php echo htmlspecialchars(!empty($hotel['image_url']) ? $hotel['image_url'] : 'publics/img/hotel-placeholder.jpg'); ?>"
            alt="<?php echo htmlspecialchars($hotel['name']); ?>" 
            class="card-img-top" 
            style="height: 200px; object-fit: cover;">
        
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($hotel['name']); ?></h5>
            <p class="card-text text-muted mb-2">
                <i class="bi bi-geo-alt-fill me-1"></i>
                <?php echo htmlspecialchars($hotel['state']) . ", " . htmlspecialchars($hotel['city']); ?>
            </p>
            <p class="card-text mb-3">
                <?php if (!empty($hotel['avg_rating'])): ?>
                    <span class="d-flex align-items-center">
                        <strong><?php echo number_format($hotel['avg_rating'], 1); ?></strong>
                        <small class="text-muted ms-1">/ 5.0</small>
                    </span>
                <?php else: ?>
                    <span class="text-muted fst-italic">No reviews yet</span>
                <?php endif; ?>
            </p>
            <a href="hotel-details.php?hotel_id=<?php echo htmlspecialchars($hotel['id']); ?>" class="stretched-link"></a>
        </div>

        <div class="position-absolute bottom-0 end-0 m-2 fs-5">
            <?php if ($hotel['lowest_price'] > 0): ?>
                <?php if (!empty($hotel['discounted_price']) && $hotel['discounted_price'] < $hotel['lowest_price']): ?>
                    <span class="text-danger text-decoration-line-through">
                        ฿<?php echo number_format($hotel['lowest_price'], 2); ?>
                    </span>
                    <span class="text-success ps-2">
                        ฿<?php echo number_format($hotel['discounted_price'], 2); ?>
                    </span>
                <?php else: ?>
                    ฿<?php echo number_format($hotel['lowest_price'], 2); ?>
                <?php endif; ?>
            <?php else: ?>
                No rooms available
            <?php endif; ?>
        </div>
    </div>
</div>