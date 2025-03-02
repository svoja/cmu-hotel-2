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

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="bg-light p-3 rounded mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="hotels.php" class="text-decoration-none">Hotels</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($hotel['name']); ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Hotel Image Column -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
                     class="card-img-top object-fit-cover"
                     style="height: 400px;"
                     alt="<?php echo htmlspecialchars($hotel['name']); ?>"
                     onerror="this.src='assets/img/hotel-placeholder.jpg'">
            </div>
        </div>

        <!-- Hotel Details Column -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h1 class="display-5 mb-4"><?php echo htmlspecialchars($hotel['name']); ?></h1>
                    
                    <!-- Location & Contact Info -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-geo-alt-fill text-primary fs-5 me-2"></i>
                            <div>
                                <strong>Location:</strong> 
                                <?php echo htmlspecialchars($hotel['location']); ?>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-circle text-primary fs-5 me-2"></i>
                            <div>
                                <strong>Owner:</strong> 
                                <?php echo htmlspecialchars($hotel['owner_name']); ?>
                                <a href="mailto:<?php echo htmlspecialchars($hotel['owner_email']); ?>" 
                                   class="text-decoration-none ms-2">
                                    <i class="bi bi-envelope-fill"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <a href="booking.php?hotel_id=<?php echo $hotel['id']; ?>" 
                           class="btn btn-primary btn-lg">
                            <i class="bi bi-calendar-check me-2"></i>
                            Book Now
                        </a>
                        <a href="#" class="btn btn-outline-secondary">
                            <i class="bi bi-info-circle me-2"></i>
                            More Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'partials/footer.php'; ?>