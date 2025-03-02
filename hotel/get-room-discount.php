<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../config/middleware.php");

hotelOwnerMiddleware(); // Restrict access to hotel owners

$response = ["success" => false, "discount_percentage" => 0, "discounted_price" => 0, "base_price" => 0, "status" => "inactive"];

if (!isset($_GET['room_type_id']) || !is_numeric($_GET['room_type_id'])) {
    $response["error"] = "Invalid room_type_id.";
    echo json_encode($response);
    exit();
}

$room_type_id = intval($_GET['room_type_id']);

try {
    // Fetch base price from room_types
    $stmt = $pdo->prepare("SELECT base_price FROM room_types WHERE id = ?");
    $stmt->execute([$room_type_id]);
    $roomType = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$roomType) {
        $response["error"] = "Room type not found.";
        echo json_encode($response);
        exit();
    }

    $response["base_price"] = floatval($roomType['base_price']); // Ensure it's a valid number

    // Fetch discount details
    $stmt = $pdo->prepare("SELECT discount_percentage, status FROM discounts WHERE room_type_id = ?");
    $stmt->execute([$room_type_id]);
    $discount = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($discount) {
        $response["discount_percentage"] = floatval($discount["discount_percentage"]);
        $response["status"] = $discount["status"];
        
        // Calculate discounted price
        $response["discounted_price"] = $response["base_price"] - ($response["base_price"] * ($response["discount_percentage"] / 100));
    } else {
        $response["discounted_price"] = $response["base_price"];
    }

    $response["success"] = true;
} catch (Exception $e) {
    $response["error"] = "Database error: " . $e->getMessage();
}

header("Content-Type: application/json");
echo json_encode($response);
?>
