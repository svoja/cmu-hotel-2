<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

require_once("partials/header.php");
require_once("partials/nav.php");
?>

<!-- ✅ Hero Section with Dark Overlay -->
<div class="d-flex align-items-center justify-content-center vh-100 text-white text-center position-relative" 
     style="background: url('public/img/cover.jpg') no-repeat center center/cover;">
    
    <!-- Dark Overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100" 
         style="background: rgba(0, 0, 0, 0.6);"></div>

    <!-- Content -->
    <div class="position-relative">
        <h1 class="fw-bold display-4">Find Your Perfect Stay</h1>
        <p class="lead mx-auto" style="max-width: 600px;">
            Explore the best hotels at unbeatable prices. Book now and experience luxury and comfort like never before!
        </p>
        <p class="lead">
            <a href="views/destinations.php" class="btn btn-lg btn-primary fw-bold shadow-lg px-4 py-3" 
               style="background-color: #e8b923; border: none; border-radius: 30px; transition: all 0.3s;">
                Book Now <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </p>
    </div>
</div>

<?php require_once("partials/footer.php"); ?> <!-- ✅ Include Footer -->