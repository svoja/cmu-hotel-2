<?php
$hotel_id = isset($_GET["hotel_id"]) ? intval($_GET["hotel_id"]) : 0;

if ($hotel_id > 0) {
    // Fetch all room types for the selected hotel and count available rooms
    $stmt = $pdo->prepare("
        SELECT 
            rt.id, rt.name, rt.capacity, rt.base_price,
            (SELECT COUNT(*) FROM rooms r WHERE r.room_type_id = rt.id AND r.status = 'available') AS available_rooms,
            COALESCE(
                (SELECT (rt.base_price - (rt.base_price * (d.discount_percentage / 100)))
                 FROM discounts d
                 WHERE d.hotel_id = rt.hotel_id 
                 AND d.room_type_id = rt.id
                 AND d.status = 'active'
                 LIMIT 1),
                rt.base_price
            ) AS discounted_price
        FROM room_types rt
        WHERE rt.hotel_id = ?
    ");
    $stmt->execute([$hotel_id]);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch images for all room types
    $room_images = [];
    $stmt = $pdo->prepare("SELECT room_types_id, image_url FROM room_type_images WHERE hotel_id = ?");
    $stmt->execute([$hotel_id]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($images as $img) {
        $room_images[$img['room_types_id']][] = $img['image_url'];
    }

    // Fetch room amenities
    $room_amenities = [];
    $stmt = $pdo->prepare("
        SELECT rta.room_type_id, a.name
        FROM room_type_amenities rta
        INNER JOIN amenities a ON rta.amenity_id = a.id
        WHERE rta.hotel_id = ?
    ");
    $stmt->execute([$hotel_id]);
    $amenities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($amenities as $am) {
        $room_amenities[$am['room_type_id']][] = $am['name'];
    }
}
?>