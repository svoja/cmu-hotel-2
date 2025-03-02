<?php require_once("partials/header.php"); ?>
<?php 
// Retrieve hotel id
$hotelId = $_GET['hotel_id'] ?? null;
if (!$hotelId) {
    echo "<p>No hotel specified.</p>";
    require_once("partials/footer.php");
    exit;
}

// Fetch hotel details from the hotels table
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->execute([$hotelId]);
$hotel = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$hotel) {
    echo "<p>Hotel not found.</p>";
    require_once("partials/footer.php");
    exit;
}

?>

<div class="container py-5">
    <nav class="mb-4">
        <?php require_once("partials/nav.php"); ?>
    </nav>
    <div class="row">
        <!-- Hotel Image -->
        <div class="col-md-6 mb-4">
            <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
                 alt="<?php echo htmlspecialchars($hotel['name']); ?>" 
                 class="img-fluid rounded">
        </div>
        <!-- Hotel Details -->
        <div class="col-md-6">
            <h1><?php echo htmlspecialchars($hotel['name']); ?></h1>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($hotel['location']); ?></p>
            <?php if (!empty($hotel['description'])): ?>
                <p><?php echo nl2br(htmlspecialchars($hotel['description'])); ?></p>
            <?php endif; ?>
            <?php if (!empty($hotel['bios'])): ?>
                <h4>About the Hotel</h4>
                <p><?php echo nl2br(htmlspecialchars($hotel['bios'])); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once("partials/footer.php"); ?>