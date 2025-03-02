<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("../config/middleware.php");
authMiddleware();
require_once("../partials/header.php");
require_once("../partials/nav.php");
?>

<main>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm rounded-4"> <!-- ✅ Increased shadow and rounded corners -->
                    <div class="card-body p-5 text-center">
                        <h4 class="fw-bold">Need Help?</h4>
                        <p class="text-muted">We're here for you. Let us know how we can assist you.</p>

                        <form method="POST" action="../auth/support-process.php" class="mt-4">
                            <div class="mb-3 text-start">
                                <label class="form-label fw-bold">Your Name</label>
                                <input type="text" name="name" class="form-control rounded-3" placeholder="Enter your name" required>
                            </div>
                            <div class="mb-3 text-start">
                                <label class="form-label fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control rounded-3" placeholder="Enter your email" required>
                            </div>
                            <div class="mb-3 text-start">
                                <label class="form-label fw-bold">Message</label>
                                <textarea name="message" class="form-control rounded-3" rows="4" placeholder="Describe your issue or question..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold">Submit Request</button>
                        </form>

                        <hr class="my-4">

                        <p class="text-muted small">
                            Need urgent help? Contact us at <strong>support@nova.com</strong> or call <strong>+66 093 195 6230</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
</main>

<?php require_once("../partials/footer.php"); ?>