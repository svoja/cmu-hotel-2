<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../controllers/RoomTypes.php");
require_once("../controllers/RoomTypeAmenities.php");
require_once("../controllers/RoomTypeImages.php");
require_once("../partials/header.php");
require_once("../partials/nav.php");
require_once("../config/middleware.php");

hotelOwnerMiddleware(); // Restrict access to hotel owners

$roomTypes = new RoomTypes($pdo);
$roomTypeAmenities = new RoomTypeAmenities($pdo);
$roomTypeImages = new RoomTypeImages($pdo);
$error = "";
$success = "";

// Fetch all hotels owned by this user
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE owner_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If no hotels exist
if (empty($hotels)) {
    die("You do not have any hotels to manage.");
}

// Get selected hotel ID
$hotel_id = isset($_GET['hotel_id']) ? intval($_GET['hotel_id']) : ($hotels[0]['id'] ?? 0);

// Fetch all room types for the selected hotel
$stmt = $pdo->prepare("SELECT * FROM room_types WHERE hotel_id = ?");
$stmt->execute([$hotel_id]);
$roomTypesList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set action mode
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'edit';
$room_type_id = 0;
$roomType = [
    'name' => '',
    'description' => '',
    'capacity' => '',
    'base_price' => ''
];
$roomTypeImages = [];
$selectedAmenities = [];

// If in edit mode and room types exist
if ($mode == 'edit' && !empty($roomTypesList)) {
    // Get selected room type ID
    $room_type_id = isset($_GET['room_type_id']) ? intval($_GET['room_type_id']) : ($roomTypesList[0]['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT * FROM room_types WHERE id = ? AND hotel_id IN (SELECT id FROM hotels WHERE owner_id = ?)");
    $stmt->execute([$room_type_id, $_SESSION['user_id']]);
    $roomType = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch Room Type Images
    $imageStmt = $pdo->prepare("SELECT * FROM room_type_images WHERE room_types_id = ?");
    $imageStmt->execute([$room_type_id]);
    $roomTypeImages = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch selected amenities for the room type
    $selectedAmenitiesStmt = $pdo->prepare("SELECT amenity_id FROM room_type_amenities WHERE room_type_id = ?");
    $selectedAmenitiesStmt->execute([$room_type_id]);
    $selectedAmenities = $selectedAmenitiesStmt->fetchAll(PDO::FETCH_COLUMN);
}

// Fetch all available amenities
$allAmenitiesStmt = $pdo->query("SELECT * FROM amenities");
$allAmenities = $allAmenitiesStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $capacity = trim($_POST['capacity']);
    $base_price = trim($_POST['base_price']);

    if (empty($name) || empty($capacity) || empty($base_price)) {
        $error = "All required fields must be filled.";
    } elseif (!is_numeric($capacity) || !is_numeric($base_price)) {
        $error = "Capacity and base price must be numbers.";
    } else {
        if ($mode == 'add') {
            // Create new room type
            $stmt = $pdo->prepare("INSERT INTO room_types (hotel_id, name, description, capacity, base_price) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$hotel_id, $name, $description, $capacity, $base_price]);
            $room_type_id = $pdo->lastInsertId();
            
            $success = "New Room Type added successfully!";
            // Redirect to edit mode for the new room type
            header("Location: manage-room-types.php?hotel_id=$hotel_id&room_type_id=$room_type_id&mode=edit");
            exit;
        } else {
            // Update existing room type
            $stmt = $pdo->prepare("UPDATE room_types SET name = ?, description = ?, capacity = ?, base_price = ? WHERE id = ?");
            $stmt->execute([$name, $description, $capacity, $base_price, $room_type_id]);
            
            $success = "Room Type updated successfully!";
        }
    }
}

?>

<main>
    <div class="row">
        <?php require_once("../partials/sidebar.php"); ?>

        <div class="col-md-8 col-lg-9">
            <div class="card shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-center">Manage Room Types</h5>
                    <p class="text-muted text-center"><?= $mode == 'add' ? 'Add a new room type' : 'Edit your room type details'; ?></p>
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
                        <input type="hidden" name="mode" value="<?= $mode; ?>">
                        <?php if ($mode == 'edit' && isset($_GET['room_type_id'])) : ?>
                            <input type="hidden" name="room_type_id" value="<?= $_GET['room_type_id']; ?>">
                        <?php endif; ?>
                    </form>

                    <!-- Mode Selection Buttons -->
                    <div class="d-flex justify-content-between mb-4">
                        <a href="?hotel_id=<?= $hotel_id; ?>&mode=add" class="btn btn-success">Add New Room Type</a>
                        
                        <?php if (!empty($roomTypesList)) : ?>
                            <a href="?hotel_id=<?= $hotel_id; ?>&mode=edit" class="btn btn-primary">Edit Existing Room Types</a>
                        <?php endif; ?>
                    </div>

                    <?php if ($mode == 'edit' && !empty($roomTypesList)) : ?>
                        <!-- Room Type Selection (only in edit mode) -->
                        <form method="GET" class="mb-4">
                            <label class="form-label fw-bold">Select Room Type</label>
                            <select name="room_type_id" class="form-select" onchange="this.form.submit()">
                                <?php foreach ($roomTypesList as $rt) : ?>
                                    <option value="<?= $rt['id']; ?>" <?= ($room_type_id == $rt['id']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($rt['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="hotel_id" value="<?= $hotel_id; ?>">
                            <input type="hidden" name="mode" value="edit">
                        </form>
                    <?php endif; ?>

                    <!-- Room Type Form (for both add and edit) -->
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Room Type Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($roomType['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($roomType['description']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Capacity</label>
                            <input type="number" name="capacity" class="form-control" value="<?= htmlspecialchars($roomType['capacity']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Base Price</label>
                            <input type="text" name="base_price" class="form-control" value="<?= htmlspecialchars($roomType['base_price']); ?>" required>
                        </div>

                        <?php if ($mode == 'edit') : ?>
                            <!-- Room Type Amenities (only in edit mode) -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Room Type Amenities</label>
                                <button type="button" class="btn btn-secondary edit-room-amenities"
                                    data-room-type-id="<?= $room_type_id; ?>"
                                    data-bs-toggle="modal" data-bs-target="#roomAmenitiesModal">
                                    Edit Amenities
                                </button>
                            </div>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-primary w-100">
                            <?= $mode == 'add' ? 'Create Room Type' : 'Update Room Type'; ?>
                        </button>
                    </form>

                    <?php if ($mode == 'edit' && $room_type_id > 0) : ?>
                        <!-- Room Type Images (only in edit mode) -->
                        <h5 class="fw-bold mt-4">Room Type Images</h5>
                        <div class="row g-3">
                            <?php foreach ($roomTypeImages as $image) : ?>
                                <div class="col-md-4">
                                    <div class="card shadow-sm">
                                        <img src="<?= htmlspecialchars($image['image_url']); ?>" class="card-img-top">
                                        <div class="card-body p-2 text-center">
                                            <form action="../hotel/delete-room-image.php" method="POST">
                                                <input type="hidden" name="image_id" value="<?= $image['id']; ?>">
                                                <input type="hidden" name="room_types_id" value="<?= $room_type_id; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <form action="../hotel/upload-room-image.php" method="POST" enctype="multipart/form-data" class="mt-3">
                            <input type="hidden" name="room_types_id" value="<?= $room_type_id; ?>">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Upload New Image</label>
                                <input type="file" name="room_image" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-success">Upload Image</button>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</main>

<!-- Amenities Modal (include only in edit mode) -->
<?php if ($mode == 'edit') : ?>
    <div class="modal fade" id="roomAmenitiesModal" tabindex="-1" aria-labelledby="roomAmenitiesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Room Amenities</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="roomAmenitiesForm">
                        <input type="hidden" name="room_type_id" id="roomTypeId">
                        
                        <div class="row">
                            <?php foreach ($allAmenities as $amenity) : ?>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input room-amenity-checkbox" type="checkbox" name="amenities[]" value="<?= $amenity['id']; ?>">
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
<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".edit-room-amenities").forEach(button => {
        button.addEventListener("click", function () {
            let roomTypeId = this.getAttribute("data-room-type-id");

            if (!roomTypeId) {
                alert("Error: Room Type ID is missing.");
                return;
            }

            document.getElementById("roomTypeId").value = roomTypeId;
            console.log("Room Type ID Set:", roomTypeId);

            // Fetch existing amenities for the room type
            fetch("../hotel/get-room-amenities.php?room_type_id=" + roomTypeId)
                .then(response => response.json())
                .then(data => {
                    console.log("Amenities received:", data); // Debugging

                    // Uncheck all checkboxes first
                    document.querySelectorAll(".room-amenity-checkbox").forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    // Check the ones that are in the fetched data
                    if (data.success && data.amenities) {
                        document.querySelectorAll(".room-amenity-checkbox").forEach(checkbox => {
                            if (data.amenities.includes(parseInt(checkbox.value))) {
                                checkbox.checked = true;
                            }
                        });
                    }
                })
                .catch(error => console.error("Error loading room amenities:", error));
        });
    });

    document.getElementById("roomAmenitiesForm").addEventListener("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        console.log("Form Data Sent:", [...formData.entries()]); // Debugging

        fetch("../hotel/update-room-amenities.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log("Server Response:", data); // Debugging

            if (data.success) {
                alert("Amenities updated successfully!");
                location.reload();
            } else {
                alert("Failed to update amenities. Error: " + data.error);
            }
        })
        .catch(error => {
            console.error("Fetch Error:", error);
            alert("Failed to connect to the server.");
        });
    });
});

</script>
<?php require_once("../partials/footer.php"); ?>