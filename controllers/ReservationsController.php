<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../config/middleware.php");

hotelOwnerMiddleware(); // Restrict access to hotel owners

class ReservationsController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all reservations for the logged-in hotel owner
    public function getAllReservations($owner_id) {
        $stmt = $this->pdo->prepare(
            "SELECT r.*, u.name AS user_name, h.name AS hotel_name 
            FROM reservations r
            JOIN users u ON r.user_id = u.id
            JOIN hotels h ON r.hotel_id = h.id
            WHERE h.owner_id = ?
            ORDER BY r.created_at DESC"
        );
        $stmt->execute([$owner_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch a specific reservation by ID
    public function getReservationById($reservation_id, $owner_id) {
        $stmt = $this->pdo->prepare(
            "SELECT r.*, u.name AS user_name, h.name AS hotel_name 
            FROM reservations r
            JOIN users u ON r.user_id = u.id
            JOIN hotels h ON r.hotel_id = h.id
            WHERE r.id = ? AND h.owner_id = ?"
        );
        $stmt->execute([$reservation_id, $owner_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update reservation status (pending, confirmed, cancelled)
    public function updateReservationStatus($reservation_id, $status, $owner_id) {
        $stmt = $this->pdo->prepare(
            "UPDATE reservations r
            JOIN hotels h ON r.hotel_id = h.id
            SET r.status = ?
            WHERE r.id = ? AND h.owner_id = ?"
        );
        return $stmt->execute([$status, $reservation_id, $owner_id]);
    }

    // Delete a reservation (if necessary)
    public function deleteReservation($reservation_id, $owner_id) {
        $stmt = $this->pdo->prepare(
            "DELETE r FROM reservations r
            JOIN hotels h ON r.hotel_id = h.id
            WHERE r.id = ? AND h.owner_id = ?"
        );
        return $stmt->execute([$reservation_id, $owner_id]);
    }
}

$controller = new ReservationsController($pdo);