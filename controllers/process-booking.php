<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../config/middleware.php");

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to book a room.";
    header("Location: ../login.php");
    exit();
}

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $hotel_id = isset($_POST['hotel_id']) ? intval($_POST['hotel_id']) : 0;
    $room_types_id = isset($_POST['room_types_id']) ? intval($_POST['room_types_id']) : 0;
    $check_in = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';

    if (!$hotel_id || !$room_types_id || empty($check_in) || empty($check_out) || empty($payment_method)) {
        $_SESSION['error'] = "Invalid booking details.";
        header("Location: ../booking.php?room_types_id=$room_types_id&hotel_id=$hotel_id");
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Get room price (apply discount if active)
        $stmt = $pdo->prepare("SELECT base_price, 
            COALESCE((SELECT (base_price - (base_price * (d.discount_percentage / 100))) 
                      FROM discounts d WHERE d.hotel_id = ? 
                      AND d.room_type_id = ? AND d.status = 'active' LIMIT 1), base_price) AS final_price
            FROM room_types WHERE id = ?");
        $stmt->execute([$hotel_id, $room_types_id, $room_types_id]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$room) {
            throw new Exception("Room type not found.");
        }

        // Calculate number of nights
        $check_in_date = new DateTime($check_in);
        $check_out_date = new DateTime($check_out);
        $nights = $check_in_date->diff($check_out_date)->days;

        if ($nights <= 0) {
            throw new Exception("Invalid check-in/check-out dates.");
        }

        // Calculate total price
        $total_price = $nights * $room['final_price'];

        // Insert reservation
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, hotel_id, check_in, check_out, total_price) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $hotel_id, $check_in, $check_out, $total_price]);
        $reservation_id = $pdo->lastInsertId();

        // Assign an available room
        $stmt = $pdo->prepare("SELECT id FROM rooms WHERE room_type_id = ? AND status = 'available' LIMIT 1");
        $stmt->execute([$room_types_id]);
        $room_id = $stmt->fetchColumn();

        if (!$room_id) {
            throw new Exception("No available rooms.");
        }

        // Reserve the room
        $stmt = $pdo->prepare("INSERT INTO reservation_rooms (reservation_id, room_id, price) VALUES (?, ?, ?)");
        $stmt->execute([$reservation_id, $room_id, $total_price]);

        // Mark the room as occupied
        $stmt = $pdo->prepare("UPDATE rooms SET status = 'occupied' WHERE id = ?");
        $stmt->execute([$room_id]);

        // Insert payment
        $payment_status = ($payment_method == 'cash') ? 'pending' : 'completed';
        $stmt = $pdo->prepare("INSERT INTO payments (reservation_id, amount, payment_method, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$reservation_id, $total_price, $payment_method, $payment_status]);

        $pdo->commit();
        $_SESSION['success'] = "Booking confirmed successfully!";
        header("Location: ../views/confirmation.php?reservation_id=$reservation_id");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error processing booking: " . $e->getMessage();
        header("Location: ../views/booking.php?room_types_id=$room_types_id&hotel_id=$hotel_id");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../index.php");
    exit();
}
?>
