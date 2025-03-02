<?php require_once("partials/header.php"); ?>

<?php
// Handle filters
$whereClauses = [];
$params = [];

if (!empty($_GET['country'])) {
    $whereClauses[] = "h.country = ?";
    $params[] = $_GET['country'];
}

if (!empty($_GET['province'])) {
    $whereClauses[] = "h.province = ?";
    $params[] = $_GET['province'];
}

if (!empty($_GET['min_price'])) {
    $whereClauses[] = "ro.price >= ?";
    $params[] = $_GET['min_price'];
}

if (!empty($_GET['max_price'])) {
    $whereClauses[] = "ro.price <= ?";
    $params[] = $_GET['max_price'];
}

// Build the query dynamically
$sql = "
    SELECT 
        h.*, 
        GROUP_CONCAT(hi.image_path) AS image_paths,
        MIN(ro.price) AS lowest_price
    FROM hotels h 
    LEFT JOIN hotel_images hi ON h.id = hi.hotel_id 
    LEFT JOIN rooms ro ON h.id = ro.hotel_id
";

// Append WHERE conditions if there are any
if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

// Group by hotel
$sql .= " GROUP BY h.id";

// Execute the query
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <nav class="mb-4"><?php require_once("partials/nav.php") ?></nav>
    <?php include('partials/country_grid.php'); ?>
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-md-3">
            <?php include("partials/filter.php"); ?>
        </div>

        <!-- Hotel Listings -->
        <div class="col-md-9">
            <?php include("partials/hotel_list.php"); ?>
        </div>
    </div>
</div>

<?php require_once("partials/footer.php"); ?>