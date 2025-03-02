<?php
require_once("../config/db.php");
require_once("../controllers/Users.php");

session_start();
$users = new Users($pdo);
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameOrEmail = trim($_POST['usernameOrEmail']);
    $password = $_POST['password'];

    if (empty($usernameOrEmail) || empty($password)) {
        $_SESSION['login_error'] = "Username/Email and Password are required.";
        header("Location: ../views/login.php");
        exit();
    }

    $user = $users->loginUser($usernameOrEmail, $password);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        header("Location: /index.php");
        exit();
    } else {
        $_SESSION['login_error'] = "Invalid username/email or password.";
        header("Location: ../views/login.php");
        exit();
    }
}
?>