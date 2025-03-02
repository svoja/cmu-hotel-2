<?php
require_once("../config/db.php");

$limit = 10; // Users per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$offset = ($page - 1) * $limit;

// Query to get total users count
$totalQuery = "SELECT COUNT(*) FROM users WHERE role != 'hotel_owner'";
if (!empty($search)) {
    $totalQuery .= " AND (name LIKE :search OR email LIKE :search)";
}
$totalStmt = $pdo->prepare($totalQuery);
if (!empty($search)) {
    $totalStmt->bindValue(":search", "%$search%", PDO::PARAM_STR);
}
$totalStmt->execute();
$totalRows = $totalStmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

// Query to fetch users with pagination
$userQuery = "SELECT id, name, email FROM users WHERE role != 'hotel_owner'";
if (!empty($search)) {
    $userQuery .= " AND (name LIKE :search OR email LIKE :search)";
}
$userQuery .= " ORDER BY name ASC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($userQuery);
if (!empty($search)) {
    $stmt->bindValue(":search", "%$search%", PDO::PARAM_STR);
}
$stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display users
if ($users) {
    echo '<ul class="list-group">';
    foreach ($users as $user) {
        echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                <span>{$user['name']} ({$user['email']})</span>
                <button class='btn btn-sm btn-primary select-user' data-id='{$user['id']}' data-name='{$user['name']}'>Select</button>
              </li>";
    }
    echo '</ul>';
} else {
    echo "<p class='text-muted text-center'>No users found.</p>";
}

echo "<nav><ul class='pagination justify-content-center mt-3'>";

// Previous Button
echo "<li class='page-item " . ($page <= 1 ? "disabled" : "") . "'>
        <a class='page-link' href='?page=" . ($page - 1) . "'>Previous</a>
      </li>";

// Page Number Links
for ($i = 1; $i <= $totalPages; $i++) {
    echo "<li class='page-item " . ($i == $page ? "active" : "") . "'>
            <a class='page-link' href='?page=" . $i . "'>" . $i . "</a>
          </li>";
}

// Next Button
echo "<li class='page-item " . ($page >= $totalPages ? "disabled" : "") . "'>
        <a class='page-link' href='?page=" . ($page + 1) . "'>Next</a>
      </li>";

echo "</ul></nav>";
?>