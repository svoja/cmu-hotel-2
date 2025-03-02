<?php
require_once '../config/db.php'; // Include your database connection

class Hotels {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all hotels
    public function getHotels() {
        $query = "SELECT * FROM hotels";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch a single hotel by ID
    public function getHotelById($id) {
        $query = "SELECT * FROM hotels WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add a new hotel
    public function addHotel($owner_id, $name, $description, $address, $city, $state, $country, $zip_code, $map_url, $phone, $email, $website) {
        $query = "INSERT INTO hotels (owner_id, name, description, address, city, state, country, zip_code, map_url, phone, email, website, created_at) 
                  VALUES (:owner_id, :name, :description, :address, :city, :state, :country, :zip_code, :map_url, :phone, :email, :website, NOW())";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':owner_id' => $owner_id,
            ':name' => $name,
            ':description' => $description,
            ':address' => $address,
            ':city' => $city,
            ':state' => $state,
            ':country' => $country,
            ':zip_code' => $zip_code,
            ':map_url' => $map_url,
            ':phone' => $phone,
            ':email' => $email,
            ':website' => $website
        ]);
    }

    // Update an existing hotel
    public function updateHotel($id, $name, $description, $address, $city, $state, $country, $zip_code, $map_url, $phone, $email, $website) {
        $query = "UPDATE hotels SET name = :name, description = :description, address = :address, city = :city, 
                  state = :state, country = :country, zip_code = :zip_code, map_url = :map_url, phone = :phone, 
                  email = :email, website = :website WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':description' => $description,
            ':address' => $address,
            ':city' => $city,
            ':state' => $state,
            ':country' => $country,
            ':zip_code' => $zip_code,
            ':map_url' => $map_url,
            ':phone' => $phone,
            ':email' => $email,
            ':website' => $website
        ]);
    }

    // Delete a hotel
    public function deleteHotel($id) {
        $query = "DELETE FROM hotels WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

// Initialize Hotels class with database connection
$hotels = new Hotels($pdo);

// Handling HTTP Requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $hotels->addHotel($_POST['owner_id'], $_POST['name'], $_POST['description'], $_POST['address'], $_POST['city'], $_POST['state'], $_POST['country'], $_POST['zip_code'], $_POST['map_url'], $_POST['phone'], $_POST['email'], $_POST['website']);
        header("Location: ../views/hotels.php?success=Hotel added successfully");
    } elseif (isset($_POST['update'])) {
        $hotels->updateHotel($_POST['id'], $_POST['name'], $_POST['description'], $_POST['address'], $_POST['city'], $_POST['state'], $_POST['country'], $_POST['zip_code'], $_POST['map_url'], $_POST['phone'], $_POST['email'], $_POST['website']);
        header("Location: ../views/hotels.php?success=Hotel updated successfully");
    } elseif (isset($_POST['delete'])) {
        $hotels->deleteHotel($_POST['id']);
        header("Location: ../views/hotels.php?success=Hotel deleted successfully");
    }
}
?>