<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../config/middleware.php");

hotelOwnerMiddleware(); // Ensure only hotel owners can manage bookings

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservation_id = isset($_POST['reservation_id']) ? intval($_POST['reservation_id']) : 0;
    $action = isset($_POST['confirm']) ? "confirm" : (isset($_POST['cancel']) ? "cancel" : null);

    if ($reservation_id > 0 && in_array($action, ["confirm", "cancel"])) {
        try {
            // Check if the reservation exists and is pending
            $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ? AND status = 'pending'");
            $stmt->execute([$reservation_id]);
            $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$reservation) {
                $_SESSION["error"] = "Reservation not found or already processed.";
            } else {
                $hotel_id = $reservation['hotel_id'];

                if ($action === "confirm") {
                    // ✅ Confirm Reservation
                    $pdo->beginTransaction();

                    // Update reservation status
                    $stmt = $pdo->prepare("UPDATE reservations SET status = 'confirmed' WHERE id = ?");
                    $stmt->execute([$reservation_id]);

                    // Update payment status
                    $stmt = $pdo->prepare("UPDATE payments SET status = 'completed' WHERE reservation_id = ?");
                    $stmt->execute([$reservation_id]);

                    // Get all room IDs for this reservation
                    $stmt = $pdo->prepare("SELECT room_id FROM reservation_rooms WHERE reservation_id = ?");
                    $stmt->execute([$reservation_id]);
                    $rooms = $stmt->fetchAll(PDO::FETCH_COLUMN);

                    // Update all rooms to occupied
                    if (!empty($rooms)) {
                        $room_ids = implode(',', array_map('intval', $rooms));
                        $stmt = $pdo->prepare("UPDATE rooms SET status = 'occupied' WHERE id IN ($room_ids)");
                        $stmt->execute();
                    }

                    $pdo->commit();
                    $_SESSION["success"] = "Reservation confirmed successfully!";
                } elseif ($action === "cancel") {
                    // ❌ Cancel Reservation
                    $pdo->beginTransaction();

                    // Update reservation status
                    $stmt = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ?");
                    $stmt->execute([$reservation_id]);

                    // Update payment status to failed (if payment exists)
                    $stmt = $pdo->prepare("UPDATE payments SET status = 'failed' WHERE reservation_id = ?");
                    $stmt->execute([$reservation_id]);

                    $pdo->commit();
                    $_SESSION["success"] = "Reservation cancelled successfully!";
                }
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION["error"] = "Database error: " . $e->getMessage();
        }
    } else {
        $_SESSION["error"] = "Invalid reservation ID or action.";
    }
}

// Redirect back to manage bookings
header("Location: manage-bookings.php");
exit();
