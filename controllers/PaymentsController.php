<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/db.php");
require_once("../config/middleware.php");

hotelOwnerMiddleware(); // Ensure only hotel owners can access

class PaymentsController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllPayments() {
        $stmt = $this->pdo->query("SELECT * FROM payments ORDER BY payment_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaymentById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM payments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addPayment($reservation_id, $amount, $payment_method, $status) {
        $stmt = $this->pdo->prepare("INSERT INTO payments (reservation_id, amount, payment_method, status) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$reservation_id, $amount, $payment_method, $status]);
    }

    public function updatePaymentStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE payments SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function deletePayment($id) {
        $stmt = $this->pdo->prepare("DELETE FROM payments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

$paymentsController = new PaymentsController($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_payment'])) {
        $reservation_id = $_POST['reservation_id'];
        $amount = $_POST['amount'];
        $payment_method = $_POST['payment_method'];
        $status = $_POST['status'];
        
        if ($paymentsController->addPayment($reservation_id, $amount, $payment_method, $status)) {
            $_SESSION['success'] = "Payment added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add payment.";
        }
        header("Location: manage-payments.php");
        exit;
    }

    if (isset($_POST['update_status'])) {
        $id = $_POST['payment_id'];
        $status = $_POST['status'];
        
        if ($paymentsController->updatePaymentStatus($id, $status)) {
            $_SESSION['success'] = "Payment status updated!";
        } else {
            $_SESSION['error'] = "Failed to update status.";
        }
        header("Location: manage-payments.php");
        exit;
    }

    if (isset($_POST['delete_payment'])) {
        $id = $_POST['payment_id'];
        
        if ($paymentsController->deletePayment($id)) {
            $_SESSION['success'] = "Payment deleted!";
        } else {
            $_SESSION['error'] = "Failed to delete payment.";
        }
        header("Location: manage-payments.php");
        exit;
    }
}