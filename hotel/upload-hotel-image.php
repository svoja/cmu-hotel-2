<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");

// Ensure user is a hotel owner
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'hotel_owner') {
    die("Unauthorized access.");
}

// Ensure hotel_id is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['hotel_id'])) {
    $hotel_id = intval($_POST['hotel_id']);

    // Check if the hotel belongs to the logged-in hotel owner
    $hotelStmt = $pdo->prepare("SELECT id FROM hotels WHERE id = ? AND owner_id = ?");
    $hotelStmt->execute([$hotel_id, $_SESSION['user_id']]);
    $hotel = $hotelStmt->fetch(PDO::FETCH_ASSOC);

    if (!$hotel) {
        die("Unauthorized action.");
    }

    // Ensure an image file is uploaded
    if (!isset($_FILES['hotel_image']) || $_FILES['hotel_image']['error'] !== UPLOAD_ERR_OK) {
        die("Image upload failed.");
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $imageFileType = strtolower(pathinfo($_FILES["hotel_image"]["name"], PATHINFO_EXTENSION));

    if (!in_array($imageFileType, $allowedExtensions)) {
        die("Invalid image format. Only JPG, PNG, and GIF are allowed.");
    }

    // Create the hotel image directory if it doesn't exist
    $hotelImagePath = "../bucket/hotel-$hotel_id/main/";
    if (!is_dir($hotelImagePath)) {
        mkdir($hotelImagePath, 0777, true);
    }

    // Move the uploaded file
    $imageName = time() . "_" . basename($_FILES["hotel_image"]["name"]);
    $targetFile = $hotelImagePath . $imageName;

    if (move_uploaded_file($_FILES["hotel_image"]["tmp_name"], $targetFile)) {
        // Save the image URL in the database
        $image_url = "/bucket/hotel-$hotel_id/main/$imageName";
        $stmt = $pdo->prepare("INSERT INTO hotel_images (hotel_id, image_url) VALUES (?, ?)");
        $stmt->execute([$hotel_id, $image_url]);

        $_SESSION['message'] = "Image uploaded successfully.";
    } else {
        $_SESSION['message'] = "Upload failed. Check folder permissions.";
    }

    // Redirect back to manage hotel page and force refresh
    header("Location: manage-hotel.php");
    header("Refresh:0");
    exit();

}

die("Invalid request.");
?>