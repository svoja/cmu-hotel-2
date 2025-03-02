<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../config/middleware.php");

hotelOwnerMiddleware(); // Ensure only hotel owners can access

$response = ["success" => false, "amenities" => []];

// Check if `room_type_id` is set
if (!isset($_GET['room_type_id']) || !is_numeric($_GET['room_type_id'])) {
    $response["error"] = "Invalid room_type_id.";
    echo json_encode($response);
    exit();
}

$room_type_id = intval($_GET['room_type_id']);

try {
    // Fetch selected amenities for the room type
    $stmt = $pdo->prepare("SELECT amenity_id FROM room_type_amenities WHERE room_type_id = ?");
    $stmt->execute([$room_type_id]);
    $selectedAmenities = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $response["success"] = true;
    $response["amenities"] = $selectedAmenities;
} catch (Exception $e) {
    $response["error"] = "Database error: " . $e->getMessage();
}

header("Content-Type: application/json");
echo json_encode($response);
?>
