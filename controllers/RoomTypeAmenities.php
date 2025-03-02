<?php
require_once '../config/db.php'; // Include your database connection

class RoomTypeAmenities {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all amenities for a specific room type
    public function getAmenitiesByRoomType($room_type_id) {
        $query = "SELECT * FROM room_type_amenities WHERE room_type_id = :room_type_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':room_type_id', $room_type_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add an amenity to a room type
    public function addAmenityToRoomType($room_type_id, $hotel_id, $amenity_id) {
        $query = "INSERT INTO room_type_amenities (room_type_id, hotel_id, amenity_id) 
                  VALUES (:room_type_id, :hotel_id, :amenity_id)";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':room_type_id' => $room_type_id,
            ':hotel_id' => $hotel_id,
            ':amenity_id' => $amenity_id
        ]);
    }

    // Remove an amenity from a room type
    public function deleteAmenityFromRoomType($room_type_id, $amenity_id) {
        $query = "DELETE FROM room_type_amenities WHERE room_type_id = :room_type_id AND amenity_id = :amenity_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':room_type_id', $room_type_id, PDO::PARAM_INT);
        $stmt->bindParam(':amenity_id', $amenity_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

// Initialize RoomTypeAmenities class with database connection
$roomTypeAmenities = new RoomTypeAmenities($pdo);

// Handling HTTP Requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $roomTypeAmenities->addAmenityToRoomType($_POST['room_type_id'], $_POST['hotel_id'], $_POST['amenity_id']);
        header("Location: ../views/room_type_amenities.php?success=Amenity added successfully");
    } elseif (isset($_POST['delete'])) {
        $roomTypeAmenities->deleteAmenityFromRoomType($_POST['room_type_id'], $_POST['amenity_id']);
        header("Location: ../views/room_type_amenities.php?success=Amenity removed successfully");
    }
}
?>