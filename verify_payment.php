<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['payment_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$user_id = $_SESSION['user_id'];
$payment_id = $_POST['payment_id'];

// Verify the payment with Razorpay API (you should implement this)
$payment_verified = true; // Replace with actual verification logic

if ($payment_verified) {
    // Update the user's payment status in the database
    $stmt = $conn->prepare("UPDATE users SET has_paid = 1 WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}