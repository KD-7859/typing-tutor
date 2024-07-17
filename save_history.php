<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    die('User not logged in');
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $wpm = $data['wpm'];
    $accuracy = $data['accuracy'];
    $errors = $data['errors'];
    $time = $data['time'];
    
    $stmt = $conn->prepare("INSERT INTO typing_history (user_id, wpm, accuracy, errors, time) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iidii", $user_id, $wpm, $accuracy, $errors, $time);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();
?>