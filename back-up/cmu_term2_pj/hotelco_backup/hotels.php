<?php
require 'config/db.php'; // Connect to MySQL
require 'helpers/s3_helper.php'; // Import S3 Helper

// Fetch all hotels
$stmt = $pdo->query("SELECT * FROM hotels");
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($hotels as $hotel) {
    // Generate the S3 Pre-Signed URL
    $imageUrl = getHotelImageUrl($hotel['name'], $hotel['image_path']);
    
    echo "<div>";
    echo "<h2><a href='hotel_details.php?hotel_id={$hotel['id']}'>{$hotel['name']}</a></h2>";
    echo "<p>Location: {$hotel['location']}</p>";
    echo "<img src='{$imageUrl}' alt='{$hotel['name']}' style='width: 300px;'><hr>";
    echo "</div>";
}
?>