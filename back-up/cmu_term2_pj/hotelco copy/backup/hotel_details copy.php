<?php
require 'config/db.php'; // Connect to MySQL
require 'helpers/s3_helper.php'; // Import S3 Helper Functions
$hotelId = $_GET['hotel_id'] ?? null;
$stmt = $pdo->prepare("SELECT hotels.*, users.name AS owner_name, users.email AS owner_email 
                       FROM hotels 
                       JOIN users ON hotels.owner_id = users.id 
                       WHERE hotels.id = ?");
$stmt->execute([$hotelId]);
$hotel = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$hotel) {
    echo "<p>Hotel not found!</p>";
    exit;
}

$imageUrl = getHotelImageUrl($hotel['name'], $hotel['image_path']);
require 'partials/header.php';
?>

    <h1><?php echo $hotel['name']; ?></h1>
    <p><strong>Location:</strong> <?php echo $hotel['location']; ?></p>
    <p><strong>Owner:</strong> <?php echo $hotel['owner_name']; ?> (<?php echo $hotel['owner_email']; ?>)</p>    
    <img src="<?php echo $imageUrl; ?>" alt="Hotel Image" style="width: 400px;">

<?php require 'partials/footer.php'; ?>