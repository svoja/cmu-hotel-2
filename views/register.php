<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

require_once("../config/middleware.php");
Middleware();

require_once("../partials/header.php"); // ✅ Include Header
require_once("../partials/nav.php"); // ✅ Include Navigation 
?>

<main>
    <div class="container mt-5 d-flex align-items-center justify-content-center">
        <div class="card shadow-sm" style="width: 380px;">
            <div class="card-body p-4">
                <h4 class="text-center mb-4 fw-normal">Create Account</h4>

                <?php if (!empty($error)) : ?>
                    <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success)) : ?>
                    <div class="alert alert-success py-2 small"><?= $success; ?></div>
                <?php endif; ?>
                
                <form action="../auth/register-process.php" method="POST"> <!-- ✅ Fix Path -->
                    <div class="mb-3">
                        <label class="form-label small text-muted">Name</label>
                        <input type="text" name="name" class="form-control form-control-lg bg-light border-0" placeholder="Ex.John" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Email</label>
                        <input type="email" name="email" class="form-control form-control-lg bg-light border-0" placeholder="Ex.Example@example.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Phone</label>
                        <input type="text" name="phone" class="form-control form-control-lg bg-light border-0" placeholder="Ex.099-XXX-XXXX" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-muted">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg bg-light border-0" placeholder="Enter a secure password" required>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 py-2 mb-3">Create Account</button>
                </form>

                <p class="text-center text-muted small">
                    Already have an account? <a href="../views/login.php" class="text-decoration-none text-dark">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</main>

<?php require_once("../partials/footer.php"); ?>