<?php
require '../config/db.php';
require '../helpers/response.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "GET") {
    // Get all hotels
    $stmt = $pdo->query("SELECT * FROM hotels");
    $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
    sendJsonResponse("success", "Hotels fetched successfully", $hotels);
} elseif ($method == "POST") {
    // Create a new hotel
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['name'], $data['location'])) {
        sendJsonResponse("error", "Missing required fields");
    }
    
    $stmt = $pdo->prepare("INSERT INTO hotels (name, location) VALUES (?, ?)");
    $stmt->execute([$data['name'], $data['location']]);
    sendJsonResponse("success", "Hotel added successfully");
} else {
    sendJsonResponse("error", "Invalid request method");
}
?>