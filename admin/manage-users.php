<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../partials/header.php");
require_once("../partials/nav.php");
require_once("../config/middleware.php");

adminMiddleware();

// Pagination Setup
$limit = 15;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$offset = ($page - 1) * $limit;

// Count Total Users
$totalQuery = "SELECT COUNT(*) FROM users WHERE role != 'admin'";
if (!empty($search)) {
    $totalQuery .= " AND (name LIKE :search OR email LIKE :search)";
}
$totalStmt = $pdo->prepare($totalQuery);
if (!empty($search)) {
    $totalStmt->bindValue(":search", "%$search%", PDO::PARAM_STR);
}
$totalStmt->execute();
$totalRows = $totalStmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

// Fetch Users with Pagination (Including Phone Number)
$userQuery = "SELECT id, name, email, phone, role FROM users WHERE role != 'admin'";
if (!empty($search)) {
    $userQuery .= " AND (name LIKE :search OR email LIKE :search)";
}
$userQuery .= " ORDER BY name ASC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($userQuery);
if (!empty($search)) {
    $stmt->bindValue(":search", "%$search%", PDO::PARAM_STR);
}
$stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <div class="row">
        <?php require_once("../partials/sidebar.php"); ?>

        <div class="col-md-8 col-lg-9">
            <div class="card shadow-sm rounded-3">
                <div class="card-body p-4">
                <?php if (!empty($_SESSION['delete_message'])) : ?>
                    <div class="alert <?= strpos($_SESSION['delete_message'], 'Error') !== false ? 'alert-danger' : 'alert-success'; ?>">
                        <?= htmlspecialchars($_SESSION['delete_message']); ?>
                    </div>
                    <?php unset($_SESSION['delete_message']); ?>
                <?php endif; ?>

                    <h5 class="fw-bold text-center mb-3">Manage Users</h5>
                    <p class="text-muted text-center">View, edit, or delete users from the system.</p>
                    <hr class="my-3">

                    <!-- Search Box -->
                    <form method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?= htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </div>
                    </form>

                    <!-- User Table -->
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)) : ?>
                                <?php foreach ($users as $index => $user) : ?>
                                    <tr>
                                        <td><?= $offset + $index + 1; ?></td>
                                        <td><?= htmlspecialchars($user['name']); ?></td>
                                        <td><?= htmlspecialchars($user['email']); ?></td>
                                        <td><?= htmlspecialchars($user['phone']); ?></td>
                                        <td>
                                            <span class="badge bg-<?= $user['role'] === 'hotel_owner' ? 'info' : 'secondary'; ?>">
                                                <?= ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning edit-user"
                                                data-id="<?= $user['id']; ?>"
                                                data-name="<?= htmlspecialchars($user['name']); ?>"
                                                data-email="<?= htmlspecialchars($user['email']); ?>"
                                                data-phone="<?= htmlspecialchars($user['phone']); ?>"
                                                data-role="<?= $user['role']; ?>"
                                                data-bs-toggle="modal" data-bs-target="#editUserModal">
                                                Edit
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-user"
                                                data-id="<?= $user['id']; ?>"
                                                data-name="<?= htmlspecialchars($user['name']); ?>"
                                                data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                                                Delete
                                            </button>

                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No users found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <nav>
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?= $page - 1; ?>">Previous</a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?= $page + 1; ?>">Next</a>
                            </li>
                        </ul>
                    </nav>

                </div>
            </div>
        </div>
    </div>
</main>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="../auth/update-user.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="user_id" id="editUserId">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" id="editUserName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="editUserEmail" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" id="editUserPhone" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" id="editUserRole" class="form-select">
                        <option value="user">User</option>
                        <option value="hotel_owner">Hotel Owner</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="../auth/delete-user.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="user_id" id="deleteUserId">
                <p>Are you sure you want to delete <strong id="deleteUserName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("click", function (event) {
    if (event.target.classList.contains("edit-user")) {
        document.getElementById("editUserId").value = event.target.dataset.id;
        document.getElementById("editUserName").value = event.target.dataset.name;
        document.getElementById("editUserEmail").value = event.target.dataset.email;
        document.getElementById("editUserPhone").value = event.target.dataset.phone;
        document.getElementById("editUserRole").value = event.target.dataset.role;
    }
});

document.addEventListener("click", function (event) {
    if (event.target.classList.contains("delete-user")) {
        document.getElementById("deleteUserId").value = event.target.dataset.id;
        document.getElementById("deleteUserName").textContent = event.target.dataset.name;
    }
});

</script>

<?php require_once("../partials/footer.php"); ?>