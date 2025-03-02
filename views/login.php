<?php 
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once('../config/middleware.php');
Middleware();

require_once("../partials/header.php"); // ✅ Include Header
require_once("../partials/nav.php"); // ✅ Include Navigation 
?>

<main>
    <div class="container mt-5 d-flex align-items-center justify-content-center">
        <div class="card shadow-sm" style="width: 380px;">
            <div class="card-body p-4">
                <h4 class="text-center mb-4 fw-normal">Sign In</h4>

                <!-- ✅ Show login error message -->
                <?php if (isset($_SESSION['login_error'])) : ?>
                    <div class="alert alert-danger small"><?= htmlspecialchars($_SESSION['login_error']); ?></div>
                    <?php unset($_SESSION['login_error']); // ✅ Clear error after showing ?>
                <?php endif; ?>

                <form action="../auth/login-process.php" method="POST"> <!-- ✅ Fixed Path -->
                    <div class="mb-3">
                        <label class="form-label small text-muted">Username or Email</label>
                        <input type="text" name="usernameOrEmail" class="form-control form-control-lg bg-light border-0" placeholder="Ex.Phanu" required>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label small text-muted">Password</label>
                        </div>
                        <input type="password" name="password" class="form-control form-control-lg bg-light border-0" required>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 py-2 mb-3">Sign In</button>
                </form>

                <p class="text-center text-muted small">
                    Don't have an account? <a href="register.php" class="text-decoration-none text-dark">Sign up</a>
                </p>
            </div>
        </div>
    </div>
</main>

<?php require_once("../partials/footer.php"); ?> <!-- ✅ Include Footer -->