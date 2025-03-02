<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../config/middleware.php");
require_once("../partials/header.php");
require_once("../partials/nav.php");

hotelOwnerMiddleware(); // Restrict access to hotel owners

// Fetch all pending reservations
$stmt = $pdo->prepare("SELECT r.*, u.name AS guest_name FROM reservations r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.status = 'pending'");
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<main>
    <div class="row">
        <?php require_once("../partials/sidebar.php"); ?>
        <div class="col-md-8 col-lg-9">
            <div class="card shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-center">Manage Bookings</h5>
                    <p class="text-muted text-center">Confirm or cancel pending reservations.</p>
                    <hr class="my-3">

                    <?php if (empty($reservations)) : ?>
                        <p class="text-center text-muted">No pending reservations.</p>
                    <?php else : ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Guest Name</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Total Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $r) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['id']); ?></td>
                                        <td><?= htmlspecialchars($r['guest_name']); ?></td>
                                        <td><?= htmlspecialchars($r['check_in']); ?></td>
                                        <td><?= htmlspecialchars($r['check_out']); ?></td>
                                        <td>฿<?= number_format($r['total_price'], 2); ?></td>
                                        <td>
                                            <form action="update-booking.php" method="POST" class="d-inline">
                                                <input type="hidden" name="reservation_id" value="<?= $r['id']; ?>">
                                                <button type="submit" name="confirm" class="btn btn-success btn-sm">Confirm</button>
                                            </form>
                                            <form action="update-booking.php" method="POST" class="d-inline">
                                                <input type="hidden" name="reservation_id" value="<?= $r['id']; ?>">
                                                <button type="submit" name="cancel" class="btn btn-danger btn-sm">Cancel</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once("../partials/footer.php"); ?>