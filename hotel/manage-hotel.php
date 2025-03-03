<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../controllers/Hotels.php");
require_once("../partials/header.php");
require_once("../partials/nav.php");
require_once("../config/middleware.php");

hotelOwnerMiddleware(); // Only hotel owners can access

$hotels = new Hotels($pdo);
$error = "";
$success = "";

// Fetch all hotels owned by this user
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE owner_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If owner has no hotels
if (empty($hotels)) {
    die("You do not have any hotels to manage.");
}

// Get selected hotel ID
$hotel_id = isset($_GET['hotel_id']) ? intval($_GET['hotel_id']) : ($hotels[0]['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ? AND owner_id = ?");
$stmt->execute([$hotel_id, $_SESSION['user_id']]);
$hotel = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch Hotel Images
$imageStmt = $pdo->prepare("SELECT * FROM hotel_images WHERE hotel_id = ?");
$imageStmt->execute([$hotel_id]);
$hotelImages = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all available amenities
$allAmenitiesStmt = $pdo->query("SELECT * FROM amenities");
$allAmenities = $allAmenitiesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch selected amenities for the hotel
$selectedAmenitiesStmt = $pdo->prepare("SELECT amenity_id FROM hotel_amenities WHERE hotel_id = ?");
$selectedAmenitiesStmt->execute([$hotel_id]);
$selectedAmenities = $selectedAmenitiesStmt->fetchAll(PDO::FETCH_COLUMN);

// Handle hotel update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $country = trim($_POST['country']);
    $zip_code = trim($_POST['zip_code']);
    $status = (!empty($_POST['status']) && $_POST['status'] === 'active') ? 'active' : 'inactive';

    // Optional Fields: Set NULL if empty
    $map_url = !empty($_POST['map_url']) ? trim($_POST['map_url']) : null;
    $phone = !empty($_POST['phone']) ? trim($_POST['phone']) : null;
    $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $website = !empty($_POST['website']) ? trim($_POST['website']) : null;

    // Validation for required fields
    if (empty($name) || empty($description) || empty($address) || empty($city) || empty($state) || empty($country) || empty($zip_code)) {
        $error = "All required fields must be filled.";
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Update hotel details
        $stmt = $pdo->prepare("UPDATE hotels SET name = ?, description = ?, address = ?, city = ?, state = ?, country = ?, zip_code = ?, map_url = ?, phone = ?, email = ?, website = ?, status = ? WHERE id = ?");
        $stmt->execute([$name, $description, $address, $city, $state, $country, $zip_code, $map_url, $phone, $email, $website, $status, $hotel_id]);

        $success = "Hotel details updated successfully!";
    }
}

?>

<main>
    <div class="row">
        <?php require_once("../partials/sidebar.php"); ?>

        <div class="col-md-8 col-lg-9">
            <div class="card shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-center">Manage My Hotels</h5>
                    <p class="text-muted text-center">Edit your hotel details.</p>
                    <hr class="my-3">

                    <!-- Show Messages -->
                    <?php if (!empty($error)) : ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($success)) : ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <!-- Hotel Selection -->
                    <form method="GET" class="mb-4">
                        <label class="form-label fw-bold">Select Hotel</label>
                        <select name="hotel_id" class="form-select" onchange="this.form.submit()">
                            <?php foreach ($hotels as $h) : ?>
                                <option value="<?= $h['id']; ?>" <?= ($hotel_id == $h['id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($h['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>

                    <!-- Hotel Edit Form -->
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hotel Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($hotel['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($hotel['description']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Address</label>
                            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($hotel['address']); ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">City</label>
                                <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($hotel['city']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">State</label>
                                <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($hotel['state']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Country</label>
                                <input type="text" name="country" class="form-control" value="<?= htmlspecialchars($hotel['country']); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">ZIP Code</label>
                            <input type="text" name="zip_code" class="form-control" value="<?= htmlspecialchars($hotel['zip_code']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Google Maps URL</label>
                            <input type="text" name="map_url" class="form-control" value="<?= htmlspecialchars($hotel['map_url']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($hotel['phone']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($hotel['email']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Website</label>
                            <input type="text" name="website" class="form-control" value="<?= htmlspecialchars($hotel['website']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hotel Amenities</label>
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#amenitiesModal">
                                Edit Amenities
                            </button>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" value="active"
                                <?= ($hotel['status'] === 'active') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="statusSwitch">Hotel is Active</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Update Hotel</button>
                    </form>

                    <!-- Hotel Images -->
                    <h5 class="fw-bold mt-4">Hotel Images</h5>
                    <div class="row g-3">
                        <?php foreach ($hotelImages as $image) : ?>
                            <div class="col-md-4">
                                <div class="card shadow-sm">
                                    <img src="<?= htmlspecialchars($image['image_url']); ?>" class="card-img-top">
                                    <div class="card-body p-2 text-center">
                                        <form action="../hotel/delete-hotel-image.php" method="POST">
                                            <input type="hidden" name="image_id" value="<?= $image['id']; ?>">
                                            <input type="hidden" name="hotel_id" value="<?= $hotel_id; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <form action="../hotel/upload-hotel-image.php" method="POST" enctype="multipart/form-data" class="mt-3">
                        <input type="hidden" name="hotel_id" value="<?= $hotel_id; ?>">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Upload New Image</label>
                            <input type="file" name="hotel_image" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-success">Upload Image</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</main>

<!-- Amenities Modal -->
<div class="modal fade" id="amenitiesModal" tabindex="-1" aria-labelledby="amenitiesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Hotel Amenities</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="amenitiesForm">
                    <input type="hidden" name="hotel_id" value="<?= $hotel_id; ?>">
                    
                    <div class="row">
                        <?php foreach ($allAmenities as $amenity) : ?>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="<?= $amenity['id']; ?>"
                                        <?= in_array($amenity['id'], $selectedAmenities) ? 'checked' : ''; ?>>
                                    <label class="form-check-label"><?= htmlspecialchars($amenity['name']); ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="modal-footer mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById("amenitiesForm").addEventListener("submit", function(e) {
    e.preventDefault();
    
    let formData = new FormData(this);

    fetch("../hotel/update-hotel-amenities.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Amenities updated successfully!");
            location.reload();
        } else {
            alert("Failed to update amenities.");
        }
    })
    .catch(error => console.error("Error:", error));
});
</script>
<?php require_once("../partials/footer.php"); ?>