<?php
require_once '../config/db.php'; // Include your database connection

class RoomTypeImages {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all images for a specific room type
    public function getImagesByRoomType($room_type_id) {
        $query = "SELECT * FROM room_type_image WHERE room_type_id = :room_type_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':room_type_id', $room_type_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a new room type image
    public function addRoomTypeImage($hotel_id, $room_type_id, $image_url, $is_primary) {
        $query = "INSERT INTO room_type_image (hotel_id, room_type_id, image_url, is_primary, created_at) 
                  VALUES (:hotel_id, :room_type_id, :image_url, :is_primary, NOW())";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':hotel_id' => $hotel_id,
            ':room_type_id' => $room_type_id,
            ':image_url' => $image_url,
            ':is_primary' => $is_primary
        ]);
    }

    // Update an image record (e.g., change primary status)
    public function updateRoomTypeImage($id, $image_url, $is_primary) {
        $query = "UPDATE room_type_image SET image_url = :image_url, is_primary = :is_primary WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':image_url' => $image_url,
            ':is_primary' => $is_primary
        ]);
    }

    // Delete a room type image
    public function deleteRoomTypeImage($id) {
        $query = "DELETE FROM room_type_image WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

// Initialize RoomTypeImage class with database connection
$roomTypeImage = new RoomTypeImages($pdo);

// Handling HTTP Requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $roomTypeImage->addRoomTypeImage($_POST['hotel_id'], $_POST['room_type_id'], $_POST['image_url'], $_POST['is_primary']);
        header("Location: ../views/room_type_image.php?success=Image added successfully");
    } elseif (isset($_POST['update'])) {
        $roomTypeImage->updateRoomTypeImage($_POST['id'], $_POST['image_url'], $_POST['is_primary']);
        header("Location: ../views/room_type_image.php?success=Image updated successfully");
    } elseif (isset($_POST['delete'])) {
        $roomTypeImage->deleteRoomTypeImage($_POST['id']);
        header("Location: ../views/room_type_image.php?success=Image deleted successfully");
    }
}
?>
