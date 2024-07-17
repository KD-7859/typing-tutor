<?php
session_start();
require_once 'db_connection.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    $stmt = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE verification_token = ?");
    $stmt->bind_param("s", $token);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = "Your email has been verified. You can now log in.";
        } else {
            $_SESSION['error'] = "Invalid verification token.";
        }
    } else {
        $_SESSION['error'] = "Error verifying email. Please try again.";
    }
    
    $stmt->close();
} else {
    $_SESSION['error'] = "No verification token provided.";
}

header("Location: login.php");
exit();
?>