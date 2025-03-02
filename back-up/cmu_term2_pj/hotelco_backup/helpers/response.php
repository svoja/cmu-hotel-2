<?php
function sendJsonResponse($status, $message, $data = []) {
    header("Content-Type: application/json");
    echo json_encode([
        "status" => $status,
        "message" => $message,
        "data" => $data
    ]);
    exit;
}
?>