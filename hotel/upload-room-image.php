<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../config/middleware.php");

// Use middleware for security
hotelOwnerMiddleware();

// Ensure room_types_id is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['room_types_id'])) {
    $room_type_id = intval($_POST['room_types_id']);

    // Get hotel ID for the room type and verify ownership
    $stmt = $pdo->prepare("
        SELECT rt.hotel_id, rt.name, h.owner_id 
        FROM room_types rt
        JOIN hotels h ON rt.hotel_id = h.id
        WHERE rt.id = ?
    ");
    $stmt->execute([$room_type_id]);
    $roomTypeData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$roomTypeData || $roomTypeData['owner_id'] != $_SESSION['user_id']) {
        die("Invalid room type or unauthorized action.");
    }

    $hotel_id = $roomTypeData['hotel_id'];
    $room_types_name = strtolower(str_replace(" ", "-", $roomTypeData['name'])); // Format folder name

    // Ensure an image file is uploaded
    if (!isset($_FILES['room_image']) || $_FILES['room_image']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = "Image upload failed. Please select a valid image file.";
        header("Location: manage-room-types.php?hotel_id=$hotel_id&room_type_id=$room_type_id&mode=edit");
        exit();
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $imageFileType = strtolower(pathinfo($_FILES["room_image"]["name"], PATHINFO_EXTENSION));

    if (!in_array($imageFileType, $allowedExtensions)) {
        $_SESSION['error'] = "Invalid image format. Only JPG, JPEG, PNG, and GIF are allowed.";
        header("Location: manage-room-types.php?hotel_id=$hotel_id&room_type_id=$room_type_id&mode=edit");
        exit();
    }

    // Create the room type image directory if it doesn't exist
    $roomImagePath = "../bucket/hotel-$hotel_id/rooms/$room_types_name/";
    if (!is_dir($roomImagePath)) {
        mkdir($roomImagePath, 0777, true);
    }

    // Move the uploaded file
    $imageName = time() . "_" . basename($_FILES["room_image"]["name"]);
    $targetFile = $roomImagePath . $imageName;

    if (move_uploaded_file($_FILES["room_image"]["tmp_name"], $targetFile)) {
        // Save the image URL in the database - using the correct table name
        $image_url = "/bucket/hotel-$hotel_id/rooms/$room_types_name/$imageName";
        
        // Changed table name from room_type_image to room_type_images to match your existing code
        $stmt = $pdo->prepare("INSERT INTO room_type_images (hotel_id, room_types_id, image_url) VALUES (?, ?, ?)");
        $stmt->execute([$hotel_id, $room_type_id, $image_url]);        

        $_SESSION['success'] = "Room image uploaded successfully.";
    } else {
        $_SESSION['error'] = "Upload failed. Check folder permissions.";
    }

    // Redirect back to manage room type page
    header("Location: manage-room-types.php?hotel_id=$hotel_id&room_type_id=$room_type_id&mode=edit");
    exit();
}

// If we reach here, it's an invalid request
$_SESSION['error'] = "Invalid request.";
header("Location: manage-room-types.php");
exit();
?>