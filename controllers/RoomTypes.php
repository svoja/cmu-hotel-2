<?php
require_once '../config/db.php'; // Include your database connection

class RoomTypes {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all room types
    public function getRoomTypes() {
        $query = "SELECT * FROM room_types";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch a single room type by ID
    public function getRoomTypeById($id) {
        $query = "SELECT * FROM room_types WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add a new room type
    public function addRoomType($hotel_id, $name, $description, $capacity, $base_price) {
        $query = "INSERT INTO room_types (hotel_id, name, description, capacity, base_price, created_at) 
                  VALUES (:hotel_id, :name, :description, :capacity, :base_price, NOW())";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':hotel_id' => $hotel_id,
            ':name' => $name,
            ':description' => $description,
            ':capacity' => $capacity,
            ':base_price' => $base_price
        ]);
    }

    // Update an existing room type
    public function updateRoomType($id, $name, $description, $capacity, $base_price) {
        $query = "UPDATE room_types SET name = :name, description = :description, capacity = :capacity, base_price = :base_price 
                  WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':description' => $description,
            ':capacity' => $capacity,
            ':base_price' => $base_price
        ]);
    }

    // Delete a room type
    public function deleteRoomType($id) {
        $query = "DELETE FROM room_types WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

// Initialize RoomTypes class with database connection
$roomTypes = new RoomTypes($pdo);

// Handling HTTP Requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $roomTypes->addRoomType($_POST['hotel_id'], $_POST['name'], $_POST['description'], $_POST['capacity'], $_POST['base_price']);
        header("Location: ../views/room_types.php?success=Room type added successfully");
    } elseif (isset($_POST['update'])) {
        $roomTypes->updateRoomType($_POST['id'], $_POST['name'], $_POST['description'], $_POST['capacity'], $_POST['base_price']);
        header("Location: ../views/room_types.php?success=Room type updated successfully");
    } elseif (isset($_POST['delete'])) {
        $roomTypes->deleteRoomType($_POST['id']);
        header("Location: ../views/room_types.php?success=Room type deleted successfully");
    }
}
?>