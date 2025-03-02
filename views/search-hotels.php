<?php
require_once("../config/db.php");

$query = isset($_GET['query']) ? trim($_GET['query']) : "";

// If the search box is empty, fetch all hotels
if (empty($query)) {
    $sql = "SELECT id FROM hotels WHERE status = 'active' ORDER BY id DESC LIMIT 9";
    $stmt = $pdo->prepare($sql);
} else {
    $sql = "SELECT id FROM hotels WHERE status = 'active' 
            AND (name LIKE :query OR city LIKE :query OR state LIKE :query OR country LIKE :query) 
            ORDER BY id DESC LIMIT 9";
    $stmt = $pdo->prepare($sql);
    $searchTerm = "%$query%";
    $stmt->bindParam(':query', $searchTerm, PDO::PARAM_STR);
}

$stmt->execute();
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Start output buffering
ob_start();
echo "<div class='row row-cols-1 row-cols-md-3 g-4'>";
if (!empty($hotels)) {
    foreach ($hotels as $hotel) {
        include '../partials/hotel-card.php';
    }
} else {
    echo "<div class='col-12'><p class='text-center text-muted'>No hotels found.</p></div>";
}
echo "</div>";

// Send the output
echo ob_get_clean();
?>
