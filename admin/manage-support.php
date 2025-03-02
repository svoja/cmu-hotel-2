<?php
require_once("../partials/header.php");
require_once("../partials/nav.php");
require_once("../config/db.php");
require_once("../config/middleware.php");
adminMiddleware();

// ✅ Pagination Setup
$limit = 15;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// ✅ Get Total Support Cases (Check if table exists)
try {
    $totalStmt = $pdo->query("SELECT COUNT(*) FROM support_requests");
    $totalRows = $totalStmt->fetchColumn();
    $totalPages = ceil($totalRows / $limit);

    // ✅ Fetch Support Requests with Pagination
    $stmt = $pdo->prepare("SELECT * FROM support_requests ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bindParam(1, $limit, PDO::PARAM_INT);
    $stmt->bindParam(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $supportRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Error fetching support requests: " . $e->getMessage() . "</div>");
}

?>
<?php if (isset($_GET['resolved'])): ?>
    <?php if ($_GET['resolved'] === 'success'): ?>
        <div class="alert alert-success text-center">Support request marked as resolved.</div>
    <?php else: ?>
        <div class="alert alert-danger text-center">Error resolving support request. Try again.</div>
    <?php endif; ?>
<?php endif; ?>

<main>
        <div class="row">
            <?php require_once("../partials/sidebar.php"); ?>

            <!-- ✅ Support Requests in a Styled Card -->
            <div class="col-md-8 col-lg-9">
                <div class="card shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-center mb-3">Support Requests</h5>
                        <p class="text-muted text-center">View and resolve customer support inquiries.</p>
                        <hr class="my-3">

                        <!-- ✅ Debugging (Show if No Data) -->
                        <?php if (empty($supportRequests)): ?>
                            <div class="alert alert-warning text-center">No support requests found.</div>
                        <?php else: ?>

                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($supportRequests as $index => $request) : ?>
                                    <tr>
                                        <td><?= $offset + $index + 1; ?></td>
                                        <td><?= htmlspecialchars($request['name']); ?></td>
                                        <td><?= htmlspecialchars($request['email']); ?></td>
                                        <td>
                                            <span class="badge bg-<?= $request['status'] === 'resolved' ? 'success' : 'warning'; ?>">
                                                <?= ucfirst($request['status']); ?>
                                            </span>
                                        </td>
                                        <td><?= date("M d, Y", strtotime($request['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#supportModal<?= $request['id']; ?>">
                                                View
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- ✅ Modal for Viewing Request -->
                                    <div class="modal fade" id="supportModal<?= $request['id']; ?>" tabindex="-1" aria-labelledby="supportModalLabel<?= $request['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Support Request</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Name:</strong> <?= htmlspecialchars($request['name']); ?></p>
                                                    <p><strong>Email:</strong> <?= htmlspecialchars($request['email']); ?></p>
                                                    <p><strong>Message:</strong></p>
                                                    <p class="border p-3 rounded bg-light"><?= nl2br(htmlspecialchars($request['message'])); ?></p>
                                                    <p><strong>Status:</strong> 
                                                        <span class="badge bg-<?= $request['status'] === 'resolved' ? 'success' : 'warning'; ?>">
                                                            <?= ucfirst($request['status']); ?>
                                                        </span>
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <?php if ($request['status'] !== 'resolved') : ?>
                                                        <form action="../admin/resolve-support.php" method="POST">
                                                            <input type="hidden" name="support_id" value="<?= $request['id']; ?>">
                                                            <button type="submit" class="btn btn-success">Mark as Resolved</button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- ✅ Pagination -->
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

                        <?php endif; ?> <!-- ✅ End Debugging Check -->
                    </div>
                </div>
            </div>

        </div>

</main>

<?php require_once("../partials/footer.php"); ?>