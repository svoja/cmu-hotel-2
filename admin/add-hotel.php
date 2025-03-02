<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../controllers/Hotels.php");
require_once("../controllers/Users.php");
require_once("../partials/header.php");
require_once("../partials/nav.php");
require_once("../config/middleware.php");

adminMiddleware();

$hotels = new Hotels($pdo);
$users = new Users($pdo);
$error = "";
$success = "";

// Fetch users who are NOT already hotel owners
$usersStmt = $pdo->query("SELECT id, name FROM users WHERE role != 'hotel_owner'");
$usersList = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $owner_id = ($_SESSION['user_role'] === 'admin') ? intval($_POST['owner_id']) : $_SESSION['user_id'];

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $country = trim($_POST['country']);
    $zip_code = trim($_POST['zip_code']);

    // Nullable fields
    $map_url = !empty($_POST['map_url']) ? trim($_POST['map_url']) : null;
    $phone = !empty($_POST['phone']) ? trim($_POST['phone']) : null;
    $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $website = !empty($_POST['website']) ? trim($_POST['website']) : null;

    // Validate required fields
    if (empty($name) || empty($description) || empty($address) || empty($city) || empty($state) || empty($country) || empty($zip_code)) {
        $error = "All required fields must be filled.";
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Update user role if selected from dropdown
        if ($_SESSION['user_role'] === 'admin' && isset($_POST['owner_id']) && $_POST['owner_id'] !== '') {
            $users->updateUserRole($_POST['owner_id'], "hotel_owner");
        }

        $hotel_id = $hotels->addHotel($owner_id, $name, $description, $address, $city, $state, $country, $zip_code, $map_url, $phone, $email, $website);

        if ($hotel_id) {
            $success = "Hotel added successfully!";
        } else {
            $error = "Failed to add hotel. Please try again.";
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
                    <h5 class="fw-bold text-center">Add New Hotel</h5>
                    <p class="text-muted text-center">Fill in the details to add a new hotel.</p>
                    <hr class="my-3">

                    <?php if (!empty($error)) : ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($success)) : ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <!-- Hotel Owner Selection (Admin Only) -->
                        <?php if ($_SESSION['user_role'] === 'admin') : ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Assign Hotel Owner</label>
                                <div class="input-group">
                                    <input type="text" id="selectedOwner" class="form-control" placeholder="Select owner" readonly>
                                    <input type="hidden" name="owner_id" id="ownerId">
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#userModal">Select</button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hotel Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Address</label>
                            <input type="text" name="address" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">City</label>
                                <input type="text" name="city" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">State</label>
                                <input type="text" name="state" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Country</label>
                                <input type="text" name="country" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">ZIP Code</label>
                                <input type="text" name="zip_code" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Google Maps URL</label>
                                <input type="text" name="map_url" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Website</label>
                            <input type="text" name="website" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add Hotel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- User Selection Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select a Hotel Owner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="userSearch" class="form-control mb-3" placeholder="Search by name or email...">
                <div id="userList"></div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    function fetchUsers(page = 1, search = '') {
        fetch(`fetch-users.php?page=${page}&search=${search}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById("userList").innerHTML = data;
            });
    }

    document.getElementById("userModal").addEventListener("show.bs.modal", function () {
        fetchUsers();
    });

    document.getElementById("userSearch").addEventListener("input", function () {
        fetchUsers(1, this.value);
    });

    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("select-user")) {
            let userId = event.target.getAttribute("data-id");
            let userName = event.target.getAttribute("data-name");

            document.getElementById("selectedOwner").value = userName;
            document.getElementById("ownerId").value = userId;
            bootstrap.Modal.getInstance(document.getElementById("userModal")).hide();
        }
    });
});
</script>
<?php require_once("../partials/footer.php"); ?>