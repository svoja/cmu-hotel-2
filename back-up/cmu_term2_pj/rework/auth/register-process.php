<?php require_once("../config/db.php"); ?>
<?php require_once("../controllers/Users.php"); ?>

<?php
$users = new Users($pdo);
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];

    // Check if user already exists
    if ($users->getUserByEmail($email)) {
        $error = "Email is already registered.";
    } else {
        $registered = $users->createUser($name, $email, $password, $phone);
        if ($registered) {
            $success = "Account created successfully! You can now <a href='login.php' class='text-dark'>Login</a>.";
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>