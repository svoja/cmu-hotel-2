<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../controllers/Users.php");
require_once("../partials/header.php");
require_once("../partials/nav.php");
require_once("../config/middleware.php");
authMiddleware();

$users = new Users($pdo);
$user_id = $_SESSION['user_id'];

// ✅ Fetch Current User Data
$stmt = $pdo->prepare("SELECT name, email, phone FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ Handle Profile Update
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // ✅ Basic Validation
    if (empty($name) || empty($email) || empty($phone)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // ✅ Update User Data (Role Stays Unchanged)
        $updated = $users->updateUser($user_id, $name, $email, $phone, $_SESSION['user_role']);

        if ($updated) {
            $_SESSION['user_name'] = $name; // ✅ Update Session Name
            $success = "Profile updated successfully!";
        } else {
            $error = "Failed to update profile. Try again.";
        }
    }
}
?>

<main>
        <div class="row">
            <!-- ✅ Include Sidebar -->
            <?php require_once("../partials/sidebar.php"); ?>

            <!-- ✅ Profile Update Card -->
            <div class="col-md-8 col-lg-9">
                <div class="card shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-center mb-3">My Profile</h5>
                        <p class="text-muted text-center">Update your personal information below.</p>
                        <hr class="my-3">

                        <!-- ✅ Success & Error Messages -->
                        <?php if (!empty($error)) : ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($success)) : ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Name</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</main>

<?php require_once("../partials/footer.php"); ?>
