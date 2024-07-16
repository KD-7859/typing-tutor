<?php
session_start();
require_once 'db_connection.php';
require_once 'email_functions.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $mobile = filter_input(INPUT_POST, 'mobile', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    error_log("Signup attempt - Username: $username, Email: $email, Mobile: $mobile");

    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $_SESSION['error'] = "Mobile number must be exactly 10 digits";
        header("Location: login.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match";
        header("Location: login.php");
        exit();
    }

    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    if ($stmt === false) {
        error_log("Error preparing statement: " . $conn->error);
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    if (!$stmt->execute()) {
        error_log("Error executing statement: " . $stmt->error);
        die("Error executing statement: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Username already exists";
        header("Location: login.php");
        exit();
    }
    $stmt->close();

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if ($stmt === false) {
        error_log("Error preparing statement: " . $conn->error);
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        error_log("Error executing statement: " . $stmt->error);
        die("Error executing statement: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email already exists";
        header("Location: login.php");
        exit();
    }
    $stmt->close();

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Generate OTP
    $otp = sprintf("%06d", mt_rand(1, 999999));
    $otp_expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    // Insert new user with unverified status
    $stmt = $conn->prepare("INSERT INTO users (username, email, mobile, password, otp, otp_expiry, is_verified) VALUES (?, ?, ?, ?, ?, ?, 0)");
    if ($stmt === false) {
        error_log("Error preparing statement: " . $conn->error);
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ssssss", $username, $email, $mobile, $hashed_password, $otp, $otp_expiry);
    if ($stmt->execute()) {
        // Send verification email
        if (sendVerificationEmail($email, $otp)) {
            $_SESSION['email'] = $email;
            $_SESSION['success'] = "Account created successfully. Please check your email for verification.";
            header("Location: verify_email.php");
            exit();
        } else {
            $_SESSION['error'] = "Error sending verification email. Please try again.";
            header("Location: login.php");
            exit();
        }
    } else {
        error_log("Error creating account: " . $stmt->error);
        $_SESSION['error'] = "Error creating account. Please try again.";
        header("Location: login.php");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>