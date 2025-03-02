<?php
require_once("../config/db.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if (empty($name) || empty($email) || empty($message)) {
        $_SESSION['support_error'] = "All fields are required.";
        header("Location: support.php");
        exit();
    }

    try {
        // ✅ Insert into Database
        $stmt = $pdo->prepare("
            INSERT INTO support_requests (user_id, name, email, message, status) 
            VALUES (?, ?, ?, ?, 'pending')
        ");
        $stmt->execute([$user_id, $name, $email, $message]);

        // ✅ Send Email Notification (Optional)
        $to = "support@nova.com"; // Change this to your support email
        $subject = "New Support Request from $name";
        $headers = "From: $email\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";

        $emailBody = "New support request received:\n\n";
        $emailBody .= "Name: $name\n";
        $emailBody .= "Email: $email\n";
        $emailBody .= "Message:\n$message\n\n";
        $emailBody .= "Check your admin panel to respond.";

        mail($to, $subject, $emailBody, $headers);

        $_SESSION['support_success'] = "Your request has been submitted successfully.";
        header("Location: ../views/support.php");
        exit();
    } catch (PDOException $e) {
        error_log("Support Request Error: " . $e->getMessage());
        $_SESSION['support_error'] = "Something went wrong. Please try again.";
        header("Location: ../views/support.php");
        exit();
    }
} else {
    header("Location: ../views/support.php");
    exit();
}
?>