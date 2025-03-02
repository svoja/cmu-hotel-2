<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../config/db.php");
require_once("../controllers/Users.php");

session_start();

// Ensure only admins can delete users
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Unauthorized access.");
}

$users = new Users($pdo);
$error = "";
$success = "";

// ✅ Process user deletion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['user_id']) || empty($_POST['user_id'])) {
        $_SESSION['delete_message'] = "Error: Invalid user ID.";
        header("Location: ../admin/manage-users.php");
        exit();
    }

    $user_id = intval($_POST['user_id']);

    // ✅ Prevent admin deletion
    $checkUserStmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $checkUserStmt->execute([$user_id]);
    $user = $checkUserStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['delete_message'] = "Error: User not found.";
    } elseif ($user['role'] === 'admin') {
        $_SESSION['delete_message'] = "Error: Cannot delete an admin account.";
    } else {
        if ($users->deleteUser($user_id)) {
            $_SESSION['delete_message'] = "User deleted successfully!";
        } else {
            $_SESSION['delete_message'] = "Error: Failed to delete user.";
        }
    }

    // ✅ Redirect back to Manage Users
    header("Location: ../admin/manage-users.php");
    exit();
}
?>