<?php require_once("../config/db.php"); ?>
<?php require_once("../controllers/Users.php"); ?>

<?php
session_start();
$users = new Users($pdo);
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameOrEmail = trim($_POST['usernameOrEmail']);
    $password = $_POST['password'];

    $user = $users->loginUser($usernameOrEmail, $password);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name']; // ✅ Use `name`
        $_SESSION['user_role'] = $user['role']; // ✅ Stores role

        // ✅ Redirect ALL Users to `index.php`
        header("Location: ../index.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>