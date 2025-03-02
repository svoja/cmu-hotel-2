<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../config/db.php");
require_once("../controllers/Users.php");
require_once("../config/middleware.php");
session_start();

adminMiddleware();

$users = new Users($pdo);
$error = "";
$success = "";

// Process user update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ✅ Check if all required fields exist
    if (!isset($_POST['user_id'], $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['role'])) {
        error_log("Validation failed: Missing fields");
        $_SESSION['update_message'] = "Error: Missing required fields.";
        header("Location: ../admin/manage-users.php");
        exit();
    }

    // ✅ Trim and sanitize input
    $user_id = intval($_POST['user_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = trim($_POST['role']);

    // ✅ Validate required fields
    if (empty($user_id) || empty($name) || empty($email) || empty($phone) || empty($role)) {
        error_log("Validation failed: One or more fields are empty");
        $_SESSION['update_message'] = "Error: All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("Validation failed: Invalid email format");
        $_SESSION['update_message'] = "Error: Invalid email format.";
    } else {
        // ✅ Attempt to update the user
        if ($users->updateUser($user_id, $name, $email, $phone, $role)) {
            error_log("User updated successfully: ID $user_id");
            $_SESSION['update_message'] = "User updated successfully!";
        } else {
            error_log("Failed to update user in DB: ID $user_id");
            $_SESSION['update_message'] = "Error: Failed to update user.";
        }
    }

    // ✅ Redirect back to Manage Users page
    header("Location: ../admin/manage-users.php");
    exit();
}
?>
