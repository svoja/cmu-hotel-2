<?php 
session_start(); 

// Ensuring that session must be start
if (session_status() !== PHP_SESSION_ACTIVE) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Session not started"]);
    exit;
}

// Importing essentials code
require 'config/settings.php'; // Connect to settings.php
require 'config/db.php'; // Connect to database
require 'helpers/s3_helper.php'; // Connect to AWS S3
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Page title -->
    <title><?php echo $title; ?></title>
    <!-- Page icon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $favicon; ?>">

    <!-- BS5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>