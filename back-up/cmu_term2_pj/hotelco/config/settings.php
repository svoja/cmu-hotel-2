<?php
$title = 'Nova';
$favicon = 'public/img/mike.png';

function getHotelImagePaths($hotelId) {
    global $pdo; // Assuming you're using PDO for database connection
    $stmt = $pdo->prepare("SELECT image_path FROM hotel_images WHERE hotel_id = ?");
    $stmt->execute([$hotelId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // Fetch all image paths as an array
}
?>