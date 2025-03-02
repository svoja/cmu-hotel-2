<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

require_once("../partials/header.php");
require_once("../partials/nav.php");
?>

<main>
</main>

<?php require_once("../partials/footer.php"); ?>