<?php
require_once("../config/db.php");

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$price = isset($_GET['price']) ? intval($_GET['price']) : 5000;  // Default max price
$rating = isset($_GET['rating']) ? intval($_GET['rating']) : '';
$state = isset($_GET['state']) ? trim($_GET['state']) : '';
$amenities = isset($_GET['amenities']) ? explode(',', $_GET['amenities']) : [];

$sql = "SELECT DISTINCT h.id 
        FROM hotels h
        JOIN room_types r ON h.id = r.hotel_id
        WHERE h.status = 'active' AND r.base_price <= ?";  // ✅ Use correct column name

$params = [$price];

if (!empty($query)) {
    $sql .= " AND (h.name LIKE ? OR h.state LIKE ?)";
    array_push($params, "%$query%", "%$query%");
}

if (!empty($state)) {
    $sql .= " AND h.state = ?";
    $params[] = $state;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<div class='row row-cols-1 row-cols-md-3 g-4'>";
foreach ($hotels as $hotel) {
    echo "<div class='col'>";
    include '../partials/hotel-card.php';
    echo "</div>";
}
echo "</div>";
?>