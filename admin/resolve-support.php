<?php
require_once("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["support_id"])) {
    $support_id = intval($_POST["support_id"]);

    try {
        // ✅ Update support request status to 'resolved'
        $stmt = $pdo->prepare("UPDATE support_requests SET status = 'resolved' WHERE id = ?");
        $stmt->execute([$support_id]);

        // ✅ Redirect back with success message
        header("Location: ../admin/manage-support.php?resolved=success");
        exit();
    } catch (PDOException $e) {
        // ✅ Log Error & Redirect with Error Message
        error_log("[" . date("Y-m-d H:i:s") . "] Error resolving support request: " . $e->getMessage() . PHP_EOL, 3, __DIR__ . "/../logs/error.log");

        header("Location: ../admin/manage-support.php?resolved=error");
        exit();
    }
}

// ✅ Redirect if accessed without POST data
header("Location: ../admin/manage-support.php");
exit();
?>