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

$maxPriceQuery = $pdo->query("SELECT MAX(base_price) FROM room_types");
$maxPrice = $maxPriceQuery->fetchColumn() ?: 10000; // Default 10,000 if no data

// Get total hotels for pagination
$totalHotelsQuery = $pdo->query("SELECT COUNT(id) FROM hotels WHERE status = 'active'");
$totalHotels = $totalHotelsQuery->fetchColumn();
$totalPages = ceil($totalHotels / $limit);

// Fetch unique cities for the filter
$cityQuery = $pdo->query("SELECT DISTINCT city FROM hotels WHERE status = 'active' ORDER BY city ASC");
$cities = $cityQuery->fetchAll(PDO::FETCH_COLUMN);

// Fetch unique amenities
$amenityQuery = $pdo->query("SELECT DISTINCT name FROM amenities ORDER BY name ASC");
$amenities = $amenityQuery->fetchAll(PDO::FETCH_COLUMN);
?>

<main>
    <div class="row">
        <!-- Filters Section -->
        <aside class="col-md-3">
            <div class="card shadow-sm p-3">
                <h5 class="fw-bold mb-3">Filters</h5>

                <!-- Price Range Filter -->
                <label class="fw-semibold">Price Range</label>
                <input type="range" class="form-range" min="0" max="<?= $maxPrice; ?>" step="100" id="priceRange">
                <p class="text-muted small">Under $<span id="priceValue"><?= $maxPrice; ?></span></p>

                <!-- Star Rating Filter -->
                <label class="fw-semibold">Star Rating</label>
                <select id="starRating" class="form-select">
                    <option value="">Any</option>
                    <option value="5">5 Stars</option>
                    <option value="4">4 Stars & up</option>
                    <option value="3">3 Stars & up</option>
                </select>

                <!-- State Filter -->
                <label class="fw-semibold mt-3">State</label>
                <select id="stateFilter" class="form-select">
                    <option value="">Any</option>
                    <?php
                    $stateQuery = $pdo->query("SELECT DISTINCT state FROM hotels WHERE status = 'active' ORDER BY state ASC");
                    $states = $stateQuery->fetchAll(PDO::FETCH_COLUMN);
                    foreach ($states as $state):
                    ?>
                        <option value="<?= htmlspecialchars($state); ?>"><?= htmlspecialchars($state); ?></option>
                    <?php endforeach; ?>
                </select>

                <!-- Amenities Filter -->
                <label class="fw-semibold mt-3">Amenities</label>
                <div class="overflow-auto" style="max-height: 150px;">
                    <?php foreach ($amenities as $amenity): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= htmlspecialchars($amenity); ?>" name="amenities[]">
                            <label class="form-check-label"><?= htmlspecialchars($amenity); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-dark w-100 mt-3" id="applyFilters">Apply Filters</button>
                    <button class="btn btn-outline-secondary w-100 mt-3" id="clearFilters">Clear</button>
                </div>

            </div>
        </aside>

        <!-- Hotels Display -->
        <section class="col-md-9">
            <!-- 🔍 Search Box -->
            <div class="mb-4">
                <input type="text" id="searchBox" class="form-control" placeholder="Search by hotel name, city, state..." autocomplete="off">
            </div>

            <!-- Hotels Results -->
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
                            <a class="page-link" href="?page=<?= $page - 1; ?>" aria-label="Previous">&laquo; Prev</a>
                        </li>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?= $page + 1; ?>" aria-label="Next">Next &raquo;</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </section>
    </div>
</main>

<script>
document.getElementById("applyFilters").addEventListener("click", function () {
    applyFilters();
});

document.getElementById("priceRange").addEventListener("input", function () {
    document.getElementById("priceValue").innerText = this.value;
});

function applyFilters() {
    let query = document.getElementById("searchBox").value.trim();
    let price = document.getElementById("priceRange").value;
    let rating = document.getElementById("starRating").value;
    let state = document.getElementById("stateFilter").value;
    let amenities = [];

    document.querySelectorAll('input[name="amenities[]"]:checked').forEach((checkbox) => {
        amenities.push(checkbox.value);
    });

    let xhr = new XMLHttpRequest();
    xhr.open("GET", "../views/filter-hotels.php?query=" + encodeURIComponent(query) + 
             "&price=" + encodeURIComponent(price) + 
             "&rating=" + encodeURIComponent(rating) + 
             "&state=" + encodeURIComponent(state) + 
             "&amenities=" + encodeURIComponent(amenities.join(',')), true);
    
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById("hotelResults").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}

document.getElementById("clearFilters").addEventListener("click", function () {
    window.location.href = window.location.pathname; // ✅ Reloads the page to reset everything
});

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
