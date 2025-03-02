<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../config/middleware.php");

class ReservationRoomsController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all reservation rooms
    public function getAllReservationRooms() {
        $stmt = $this->pdo->query("SELECT * FROM reservation_rooms");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get reservation rooms by reservation ID
    public function getRoomsByReservation($reservation_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM reservation_rooms WHERE reservation_id = ?");
        $stmt->execute([$reservation_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a reservation room
    public function addReservationRoom($reservation_id, $room_id, $price) {
        $stmt = $this->pdo->prepare("INSERT INTO reservation_rooms (reservation_id, room_id, price) VALUES (?, ?, ?)");
        return $stmt->execute([$reservation_id, $room_id, $price]);
    }

    // Update reservation room price
    public function updateReservationRoom($id, $price) {
        $stmt = $this->pdo->prepare("UPDATE reservation_rooms SET price = ? WHERE id = ?");
        return $stmt->execute([$price, $id]);
    }

    // Delete a reservation room
    public function deleteReservationRoom($id) {
        $stmt = $this->pdo->prepare("DELETE FROM reservation_rooms WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

// Initialize controller
$reservationRoomsController = new ReservationRoomsController($pdo);