<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}
require_once"config/middleware.php";
Middleware();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="public/css/style.css">
    <link rel="icon" type="image/x-icon" href="public/img/mike.png">
</head>
<body>
    <div class="container">
        <?php require_once("partials/nav.php"); ?>

        <main>
        <div class="container mt-5 d-flex align-items-center justify-content-center">
    <div class="card shadow border-0" style="width: 380px;">
            <div class="card-body p-4">
                    <h4 class="text-center mb-4 fw-normal">Create Account</h4>

                    <?php if (!empty($error)) : ?>
                        <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)) : ?>
                        <div class="alert alert-success py-2 small"><?= $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small text-muted">Name</label>
                            <input type="text" name="name" class="form-control form-control-lg bg-light border-0" required>
                        </div>
                    
                        <div class="mb-3">
                            <label class="form-label small text-muted">Email</label>
                            <input type="email" name="email" class="form-control form-control-lg bg-light border-0" required>
                        </div>
                    
                        <div class="mb-3">
                            <label class="form-label small text-muted">Phone</label>
                            <input type="text" name="phone" class="form-control form-control-lg bg-light border-0" required>
                        </div>
                    
                        <div class="mb-4">
                            <label class="form-label small text-muted">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg bg-light border-0" required>
                        </div>
                    
                        <button type="submit" class="btn btn-dark w-100 py-2 mb-3">Create Account</button>
                    </form>
                    
                    <p class="text-center text-muted small">
                        Already have an account? <a href="login.php" class="text-decoration-none text-dark">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
        </main>

        <div class="container">
      <footer class="py-3 my-4">
        <p class="text-center text-body-secondary">© 2025 NOVA, Inc</p>
      </footer>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>