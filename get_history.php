<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'User not logged in']));
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT wpm, accuracy, errors, time, created_at FROM typing_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = [
        'wpm' => $row['wpm'],
        'accuracy' => $row['accuracy'],
        'errors' => $row['errors'],
        'time' => $row['time'],
        'date' => $row['created_at']
    ];
}

echo json_encode(['status' => 'success', 'history' => $history]);

$stmt->close();
$conn->close();
?>