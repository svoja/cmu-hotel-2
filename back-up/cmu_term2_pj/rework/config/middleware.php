<?php
// ADMIN
function adminMiddleware() {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != "admin") {
        header("Location: login.php");
        exit();
    }
}

// HOTEL OWNER
function hotelOwnerMiddleware() {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != "hotel_owner") {
        header("Location: login.php");
        exit();
    }
}

// NORMAL USER
function authMiddleware() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function Middleware() {
    if (isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit();
    }
}
?>