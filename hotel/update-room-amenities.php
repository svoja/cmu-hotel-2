<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../config/middleware.php");

hotelOwnerMiddleware(); // Ensure only hotel owners can update amenities

$response = ["success" => false, "error" => "Unknown error", "debug" => []];

// Step 1: Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $response["error"] = "Invalid request method. Only POST is allowed.";
    echo json_encode($response);
    exit();
}

// Step 2: Log full POST request for debugging
$response["debug"]["raw_POST"] = $_POST;

// Step 3: Check if room_type_id is received
if (!isset($_POST['room_type_id']) || !is_numeric($_POST['room_type_id'])) {
    $response["error"] = "Invalid or missing room_type_id.";
    echo json_encode($response);
    exit();
}

$room_type_id = intval($_POST['room_type_id']);
$selectedAmenities = isset($_POST['amenities']) ? $_POST['amenities'] : [];

$response["debug"]["received_room_type_id"] = $room_type_id;
$response["debug"]["received_amenities"] = $selectedAmenities;

try {
    // Fetch the hotel_id for the given room_type_id
    $stmt = $pdo->prepare("SELECT hotel_id FROM room_types WHERE id = ?");
    $stmt->execute([$room_type_id]);
    $hotel = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$hotel) {
        $response["error"] = "Room type not found in the database.";
        echo json_encode($response);
        exit();
    }

    $hotel_id = $hotel['hotel_id'];
    $response["debug"]["fetched_hotel_id"] = $hotel_id; // Log fetched hotel_id

    $pdo->beginTransaction(); // Start transaction

    // Remove existing amenities
    $stmt = $pdo->prepare("DELETE FROM room_type_amenities WHERE room_type_id = ?");
    $stmt->execute([$room_type_id]);
    $response["debug"]["deleted_existing_amenities"] = true;

    // Insert new amenities
    if (!empty($selectedAmenities)) {
        $stmt = $pdo->prepare("INSERT INTO room_type_amenities (room_type_id, hotel_id, amenity_id) VALUES (?, ?, ?)");
        foreach ($selectedAmenities as $amenity_id) {
            if (!is_numeric($amenity_id)) continue; // Prevent SQL injection
            $stmt->execute([$room_type_id, $hotel_id, $amenity_id]);
        }
    }

    $pdo->commit(); // Commit transaction
    $response["success"] = true;
    $response["error"] = null;
} catch (Exception $e) {
    $pdo->rollBack(); // Rollback if error occurs
    $response["error"] = "Database error: " . $e->getMessage();
}

// Return full response
header("Content-Type: application/json");
echo json_encode($response);
?>
