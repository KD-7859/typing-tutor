<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $verification_token = bin2hex(random_bytes(16));

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email already exists.";
        header("Location: register.php");
        exit();
    }

    // Insert user details into a temporary table
    $stmt = $conn->prepare("INSERT INTO temp_users (username, email, password, verification_token) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $verification_token);

    if ($stmt->execute()) {
        $temp_user_id = $stmt->insert_id;
        $_SESSION['temp_user_id'] = $temp_user_id;
        
        // Send verification email
        $to = $email;
        $subject = "Email Verification";
        $message = "Click the following link to verify your email: http://yourdomain.com/verify.php?token=$verification_token";
        $headers = "From: noreply@yourdomain.com";

        if (mail($to, $subject, $message, $headers)) {
            $_SESSION['success'] = "Registration successful. Please check your email to verify your account.";
            header("Location: verify.php");
            exit();
        } else {
            $_SESSION['error'] = "Error sending verification email.";
            header("Location: register.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Error registering user.";
        header("Location: register.php");
        exit();
    }

    $stmt->close();
}
?>