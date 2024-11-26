<?php
// Start session and check login status
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("Location: ../shared/login.php");
    exit;
}

// Include database configuration
require_once('C:\xampp\htdocs\attendance-roster\config.php');

// Get user ID from URL
$user_id = $_GET['id'];

// Fetch user details from database
$sql = "SELECT id, fullname, email, role FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update user data if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $update_sql = "UPDATE user SET fullname = ?, email = ?, role = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $fullname, $email, $role, $user_id);
    $update_stmt->execute();

    header("Location: ../admin/index.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="edit_delete.css">
</head>
<body>
    <div class="container">
    <h2>Edit User</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="fullname" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" id="role" name="role">
                <option value="teacher" <?php if ($user['role'] == 'teacher') echo 'selected'; ?>>Teacher</option>
                <option value="student" <?php if ($user['role'] == 'student') echo 'selected'; ?>>Student</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

</body>
</html>
