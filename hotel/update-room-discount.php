<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../config/middleware.php");

hotelOwnerMiddleware(); // Ensure only hotel owners can update discounts

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_type_id = intval($_POST['room_type_id']);
    $discount_percentage = floatval($_POST['discount_percentage']);
    $discount_status = $_POST['discount_status'];

    if ($room_type_id > 0 && $discount_percentage >= 0 && $discount_percentage <= 100) {
        try {
            // Fetch hotel_id from room_types table
            $stmt = $pdo->prepare("SELECT hotel_id FROM room_types WHERE id = ?");
            $stmt->execute([$room_type_id]);
            $room = $stmt->fetch(PDO::FETCH_ASSOC);
            $hotel_id = $room['hotel_id'] ?? null;

            if (!$hotel_id) {
                $response["error"] = "Failed to retrieve hotel ID.";
                echo json_encode($response);
                exit();
            }

            // Check if a discount already exists for this room_type_id
            $stmt = $pdo->prepare("SELECT id FROM discounts WHERE room_type_id = ?");
            $stmt->execute([$room_type_id]);
            $existingDiscount = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingDiscount) {
                // Update existing discount
                $stmt = $pdo->prepare("UPDATE discounts SET discount_percentage = ?, status = ? WHERE room_type_id = ?");
                $stmt->execute([$discount_percentage, $discount_status, $room_type_id]);
            } else {
                // Insert new discount if none exists
                $stmt = $pdo->prepare("INSERT INTO discounts (hotel_id, room_type_id, discount_percentage, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$hotel_id, $room_type_id, $discount_percentage, $discount_status]);
            }

            $response["success"] = true;
        } catch (Exception $e) {
            $response["error"] = "Database error: " . $e->getMessage();
        }
    } else {
        $response["error"] = "Invalid room type ID or discount percentage.";
    }
}

header("Content-Type: application/json");
echo json_encode($response);
?>
