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

// Get total hotels for pagination
$totalHotelsQuery = $pdo->query("SELECT COUNT(id) FROM hotels WHERE status = 'active'");
$totalHotels = $totalHotelsQuery->fetchColumn();
$totalPages = ceil($totalHotels / $limit);
?>

<main>
    <!-- 🔍 Search Box -->
    <div class="mb-4">
        <input type="text" id="searchBox" class="form-control" placeholder="Search by hotel name, city, state..." autocomplete="off">
    </div>

    <!-- Hotels Display (Results will be updated here) -->
    <div id="hotelResults">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php
            $sql = "SELECT id FROM hotels WHERE status = 'active' ORDER BY id DESC LIMIT ? OFFSET ?";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            $stmt->bindParam(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($hotels)):
                foreach ($hotels as $hotel):
                    include '../partials/hotel-card.php';
                endforeach;
            else:
                echo "<div class='col-12'><p class='text-center text-muted'>No hotels found.</p></div>";
            endif;
            ?>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo; Prev</span>
                    </a>
                </li>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= $page >= $totalPages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">Next &raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</main>

<script>
document.getElementById("searchBox").addEventListener("keyup", function () {
    let query = this.value.trim();
    let xhr = new XMLHttpRequest();
    
    xhr.open("GET", "search-hotels.php?query=" + encodeURIComponent(query), true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById("hotelResults").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
});

</script>

<?php require_once("../partials/footer.php"); ?>
