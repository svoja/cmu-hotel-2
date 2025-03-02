<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../config/middleware.php");

hotelOwnerMiddleware(); // Only hotel owners can access

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hotel_id = intval($_POST['hotel_id']);
    $selectedAmenities = isset($_POST['amenities']) ? $_POST['amenities'] : [];

    if ($hotel_id > 0) {
        // Remove all existing amenities for this hotel
        $stmt = $pdo->prepare("DELETE FROM hotel_amenities WHERE hotel_id = ?");
        $stmt->execute([$hotel_id]);

        // Add selected amenities
        if (!empty($selectedAmenities)) {
            $stmt = $pdo->prepare("INSERT INTO hotel_amenities (hotel_id, amenity_id) VALUES (?, ?)");
            foreach ($selectedAmenities as $amenity_id) {
                $stmt->execute([$hotel_id, $amenity_id]);
            }
        }

        $response["success"] = true;
    }
}

header("Content-Type: application/json");
echo json_encode($response);
?>