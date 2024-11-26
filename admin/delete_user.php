<?php
session_start();

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../shared/login.php");
    exit();
}

// Include the configuration file for the database connection
require_once '../config.php';  // Relative path from admin folder to root

// Check if the user ID is provided in the URL
if (!isset($_GET['id'])) {
    echo "User ID not specified.";
    exit();
}

$user_id = $_GET['id'];

// Delete the user from the database
$delete_query = "DELETE FROM user WHERE id = ?";
$stmt = $conn->prepare($delete_query);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    header("Location: ../admin/index.php?success=deleted");
} else {
    header("Location: ../admin/index.php?error=deletion_failed");
}

$stmt->close();
$conn->close();
exit();
?>
