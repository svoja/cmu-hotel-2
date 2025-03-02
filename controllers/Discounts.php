<?php
require_once '../config/db.php'; // Include your database connection

class Discounts {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all discounts
    public function getAllDiscounts() {
        $query = "SELECT * FROM discounts";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch discounts for a specific room type
    public function getDiscountsByRoomType($room_type_id) {
        $query = "SELECT * FROM discounts WHERE room_type_id = :room_type_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':room_type_id', $room_type_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a new discount
    public function addDiscount($hotel_id, $room_type_id, $discount_percentage, $status) {
        $query = "INSERT INTO discounts (hotel_id, room_type_id, discount_percentage, status, created_at) 
                  VALUES (:hotel_id, :room_type_id, :discount_percentage, :status, NOW())";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':hotel_id' => $hotel_id,
            ':room_type_id' => $room_type_id,
            ':discount_percentage' => $discount_percentage,
            ':status' => $status
        ]);
    }

    // Update an existing discount
    public function updateDiscount($id, $discount_percentage, $status) {
        $query = "UPDATE discounts SET discount_percentage = :discount_percentage, status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':discount_percentage' => $discount_percentage,
            ':status' => $status
        ]);
    }

    // Delete a discount
    public function deleteDiscount($id) {
        $query = "DELETE FROM discounts WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

// Initialize Discounts class with database connection
$discounts = new Discounts($pdo);

// Handling HTTP Requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $discounts->addDiscount($_POST['hotel_id'], $_POST['room_type_id'], $_POST['discount_percentage'], $_POST['status']);
        header("Location: ../views/discounts.php?success=Discount added successfully");
    } elseif (isset($_POST['update'])) {
        $discounts->updateDiscount($_POST['id'], $_POST['discount_percentage'], $_POST['status']);
        header("Location: ../views/discounts.php?success=Discount updated successfully");
    } elseif (isset($_POST['delete'])) {
        $discounts->deleteDiscount($_POST['id']);
        header("Location: ../views/discounts.php?success=Discount deleted successfully");
    }
}
?>