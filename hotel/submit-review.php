<?php
session_start();
require_once("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $hotel_id = intval($_POST['hotel_id']);
    $rating = intval($_POST['rating']);
    $review_text = trim($_POST['review_text']);

    if ($rating < 1 || $rating > 5 || empty($review_text)) {
        $_SESSION['error'] = "Invalid review.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO reviews (user_id, hotel_id, rating, review_text, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $hotel_id, $rating, $review_text]);

        $_SESSION['success'] = "Review submitted!";
    }
}
header("Location: ../views/hotel-details.php?hotel_id=$hotel_id");
exit();
?>