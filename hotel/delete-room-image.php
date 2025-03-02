<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");

// Ensure user is a hotel owner
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'hotel_owner') {
    die("Unauthorized access.");
}

// Ensure image_id is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['image_id'])) {
    $image_id = intval($_POST['image_id']);

    // Get the image details
    $stmt = $pdo->prepare("SELECT * FROM room_type_images WHERE id = ?");
    $stmt->execute([$image_id]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$image) {
        die("Image not found.");
    }

    // Check if the hotel belongs to the logged-in hotel owner
    $hotelStmt = $pdo->prepare("
        SELECT h.id 
        FROM hotels h
        JOIN room_types rt ON h.id = rt.hotel_id
        JOIN room_type_images rti ON rt.id = rti.room_types_id
        WHERE rti.id = ? AND h.owner_id = ?
    ");
    $hotelStmt->execute([$image_id, $_SESSION['user_id']]);
    $hotel = $hotelStmt->fetch(PDO::FETCH_ASSOC);

    if (!$hotel) {
        die("Unauthorized action.");
    }

    // Delete the image file from the directory
    $imagePath = ".." . $image['image_url']; // Convert URL to file path
    if (file_exists($imagePath)) {
        unlink($imagePath); // Delete file
    }

    // Delete the image record from the database
    $stmt = $pdo->prepare("DELETE FROM room_type_images WHERE id = ?");
    $stmt->execute([$image_id]);

    // Redirect back with success message
    $_SESSION['message'] = "Room image deleted successfully.";
    header("Location: manage-room-types.php?hotel_id=" . $hotel['id']);
    exit();
}

die("Invalid request.");
?>