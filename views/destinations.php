<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

require_once("../partials/header.php");
require_once("../partials/nav.php");
require_once("../config/db.php");

$limit = 9; // Number of hotels per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$totalHotelsQuery = $pdo->query("SELECT COUNT(id) FROM hotels");
$totalHotels = $totalHotelsQuery->fetchColumn();
$totalPages = ceil($totalHotels / $limit);

$sql = "SELECT id FROM hotels WHERE status = 'active' ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(1, $limit, PDO::PARAM_INT);
$stmt->bindParam(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
        <div class="row">

            <div class="col-md-12">
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php if (!empty($hotels)): ?>
                        <?php foreach ($hotels as $hotel): ?>
                            <?php include '../partials/hotel-card.php'; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p class="text-center text-muted">No hotels found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- Prev Button -->
                    <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?= $page - 1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo; Prev</span>
                        </a>
                    </li>

                    <!-- Page Numbers -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next Button -->
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?= $page + 1; ?>" aria-label="Next">
                            <span aria-hidden="true">Next &raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
</main>

<?php require_once("../partials/footer.php"); ?>