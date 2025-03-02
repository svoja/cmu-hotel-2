<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../controllers/Hotels.php");
require_once("../partials/header.php");
require_once("../partials/nav.php");
require_once("../config/middleware.php");

hotelOwnerMiddleware(); // Ensure only hotel owners can access

$hotels = new Hotels($pdo);
$error = "";
$success = "";

// Fetch hotels owned by the logged-in hotel owner
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE owner_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($hotels)) {
    die("You do not have any hotels to manage.");
}

// Select hotel ID
$hotel_id = isset($_GET['hotel_id']) ? intval($_GET['hotel_id']) : ($hotels[0]['id'] ?? 0);

// Fetch selected hotel details
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ? AND owner_id = ?");
$stmt->execute([$hotel_id, $_SESSION['user_id']]);
$hotel = $stmt->fetch(PDO::FETCH_ASSOC);

$roomTypeStmt = $pdo->prepare("SELECT * FROM room_types WHERE hotel_id = ?");
$roomTypeStmt->execute([$hotel_id]);
$roomTypes = $roomTypeStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch rooms for selected hotel
$roomStmt = $pdo->prepare("SELECT r.*, rt.name AS room_type_name
    FROM rooms r
    LEFT JOIN room_types rt ON r.room_type_id = rt.id
    WHERE r.hotel_id = ?");
$roomStmt->execute([$hotel_id]);
$rooms = $roomStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Room Addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_room'])) {
    $room_type_id = intval($_POST['room_type_id']);
    $room_number = trim($_POST['room_number']);
    $status = trim($_POST['status']);

    if (empty($room_type_id) || empty($room_number) || empty($status)) {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO rooms (hotel_id, room_type_id, room_number, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$hotel_id, $room_type_id, $room_number, $status]);
        $success = "Room added successfully!";
        header("Location: manage-rooms.php?hotel_id=" . $hotel_id);
        exit();        
    }
}

// Handle Room Status Update (Switch)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $room_id = intval($_POST['room_id']);
    $new_status = $_POST['new_status'];
    $stmt = $pdo->prepare("UPDATE rooms SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $room_id]);
    $success = "Room status updated!";
    header("Location: manage-rooms.php?hotel_id=" . $hotel_id);
    exit();
}

?>

<main>
    <div class="row">
        <?php require_once("../partials/sidebar.php"); ?>

        <div class="col-md-8 col-lg-9">
            <div class="card shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-center">Manage Rooms</h5>
                    <p class="text-muted text-center">Manage rooms, status, and assigned guests.</p>
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

                    <!-- Add Room Form -->
                    <form method="POST">
                        <input type="hidden" name="add_room" value="1">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Room Type</label>
                            <select name="room_type_id" class="form-select" required>
                                <option value="" disabled selected>Select Room Type</option>
                                <?php foreach ($roomTypes as $type) : ?>
                                    <option value="<?= $type['id']; ?>"><?= htmlspecialchars($type['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Room Number</label>
                            <input type="text" name="room_number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select">
                                <option value="available" <?= isset($room['status']) && $room['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                                <option value="occupied" <?= isset($room['status']) && $room['status'] === 'occupied' ? 'selected' : ''; ?>>Occupied</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add Room</button>
                    </form>

                    <!-- Room List -->
                    <h5 class="fw-bold mt-4">Rooms in <?= htmlspecialchars($hotel['name']); ?></h5>
                    <table class="table table-hover mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Room Number</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rooms as $index => $room) : ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td><?= htmlspecialchars($room['room_number']); ?></td>
                                <td><?= htmlspecialchars($room['room_type_name']); ?></td>
                                <td>
                                    <form method="POST" action="manage-rooms.php" class="d-inline">
                                        <input type="hidden" name="room_id" value="<?= $room['id']; ?>">
                                        <input type="hidden" name="update_status" value="1">
                                        <select name="new_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="available" <?= ($room['status'] == 'available') ? 'selected' : ''; ?>>Available</option>
                                            <option value="occupied" <?= ($room['status'] == 'occupied') ? 'selected' : ''; ?>>Occupied</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <form action="delete-room.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this room?');">
                                        <input type="hidden" name="room_id" value="<?= $room['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener("click", function (event) {
    if (event.target.classList.contains("delete-room")) {
        let roomId = event.target.dataset.id;
        if (confirm("Are you sure you want to delete this room?")) {
            fetch("../hotel/delete-room.php", {
                method: "POST",
                body: JSON.stringify({ room_id: roomId }),
                headers: { "Content-Type": "application/json" }
            }).then(() => location.reload());
        }
    }
});
</script>

<?php require_once("../partials/footer.php"); ?>
