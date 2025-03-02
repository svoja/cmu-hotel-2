<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../config/middleware.php");

hotelOwnerMiddleware(); // Ensure only hotel owners can delete rooms

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;

    if ($room_id > 0) {
        // Ensure the room belongs to the logged-in hotel owner
        $stmt = $pdo->prepare("SELECT r.id FROM rooms r
                               JOIN hotels h ON r.hotel_id = h.id
                               WHERE r.id = ? AND h.owner_id = ?");
        $stmt->execute([$room_id, $_SESSION['user_id']]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($room) {
            // Delete the room
            $deleteStmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
            $deleteStmt->execute([$room_id]);

            $_SESSION['success'] = "Room deleted successfully!";
        } else {
            $_SESSION['error'] = "Unauthorized action. Room not found.";
        }
    } else {
        $_SESSION['error'] = "Invalid room selection.";
    }
}

// Redirect back to manage rooms page
header("Location: manage-rooms.php");
exit();
?>