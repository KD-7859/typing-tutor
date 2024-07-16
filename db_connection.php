<?php
$host = "localhost";
$username = "root";  // Change this to your actual MySQL username
$password = "";      // Change this to your actual MySQL password (leave empty if no password)
$database = "login"; // Make sure this database exists
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>