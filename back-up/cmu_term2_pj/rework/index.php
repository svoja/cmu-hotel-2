<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

require_once("partials/header.php"); // ✅ Include Header
?>

    <?php require_once("partials/nav.php"); ?> <!-- ✅ Include Navigation -->

    <main>
        <!-- ✅ Hero Section -->
        <section class="py-5 text-center">
            <h1 class="fw-bold">Welcome to Nova</h1>
            <p class="lead text-muted">Discover and book the best hotels at amazing prices.</p>
            <a href="hotels.php" class="btn btn-primary">Explore Hotels</a>
        </section>

        <!-- ✅ Featured Hotels -->
        <section class="my-5">
            <h2 class="fw-bold text-center mb-4">Featured Hotels</h2>
            <?php require_once("partials/featured-hotels.php"); ?> 
        </section>
    </main>

<?php require_once("partials/footer.php"); ?> <!-- ✅ Include Footer -->