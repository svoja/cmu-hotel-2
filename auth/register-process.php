<?php
require_once("../config/db.php");
require_once("../controllers/Users.php");

session_start();
$users = new Users($pdo);
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);
    $role = "user"; // Default role

    if (empty($name) || empty($email) || empty($password) || empty($phone)) {
        $_SESSION['register_error'] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['register_error'] = "Invalid email format.";
    } elseif ($users->getUserByEmail($email)) {
        $_SESSION['register_error'] = "Email is already registered.";
    } else {
        // ✅ Create user and get the user ID
        $userId = $users->createUser($name, $email, $password, $phone, $role);

        if ($userId) {
            $_SESSION['register_success'] = "Account created successfully! You can now log in.";

            // ✅ Clear session to prevent auto-login
            session_unset();
            session_destroy();

            header("Location: ../views/login.php");
            exit();
        } else {
            $_SESSION['register_error'] = "Registration failed. Please try again.";
        }
    }

    // ✅ Redirect back to registration page if failed
    header("Location: ../views/register.php");
    exit();
}
?>