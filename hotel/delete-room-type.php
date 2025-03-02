<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../config/middleware.php");

hotelOwnerMiddleware(); // Ensure only hotel owners can delete room types

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['room_type_id'])) {
    $room_type_id = intval($_POST['room_type_id']);

    if ($room_type_id > 0) {
        try {
            // Check if the room type exists and belongs to the hotel owner
            $stmt = $pdo->prepare("SELECT hotel_id FROM room_types WHERE id = ?");
            $stmt->execute([$room_type_id]);
            $roomType = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$roomType) {
                $response["error"] = "Room type not found.";
            } else {
                $hotel_id = $roomType["hotel_id"];

                // Delete related data
                $pdo->prepare("DELETE FROM room_type_images WHERE room_types_id = ?")->execute([$room_type_id]);
                $pdo->prepare("DELETE FROM room_type_amenities WHERE room_type_id = ?")->execute([$room_type_id]);
                $pdo->prepare("DELETE FROM discounts WHERE room_type_id = ?")->execute([$room_type_id]);

                // Delete room type
                $pdo->prepare("DELETE FROM room_types WHERE id = ?")->execute([$room_type_id]);

                $response["success"] = true;
            }
        } catch (Exception $e) {
            $response["error"] = "Database error: " . $e->getMessage();
        }
    } else {
        $response["error"] = "Invalid room type ID.";
    }
}

header("Content-Type: application/json");
echo json_encode($response);
?>