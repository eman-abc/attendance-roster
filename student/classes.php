<?php
// Start session
session_start();

// Check if the user is logged in and is a student
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'student') {
    header("location: ../login.php");
    exit();
}

// Include database configuration
require_once('../config.php');

// Fetch classes and attendance for the selected semester
$student_id = $_SESSION['id'];
$semester_id = isset($_GET['semester_id']) ? $_GET['semester_id'] : 0;

$sql = "SELECT c.id AS class_id, c.name AS class_name, c.start_time, c.end_time, 
               ROUND(SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) / COUNT(a.id) * 100, 2) AS attendance_percentage
        FROM classes c
        INNER JOIN attendance a ON c.id = a.class_id
        WHERE c.semester_id = ? AND a.student_id = ?
        GROUP BY c.id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $semester_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();

$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes for Semester</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/classes.css">
</head>

<body>
    <div class="container">
        <h1 class="my-4 text-center ">Classes for Semester</h1>
        <div class="row">
            <?php if (count($classes) > 0): ?>
                <?php foreach ($classes as $class): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($class['class_name']) ?></h5>
                                <p><strong>Start Time:</strong> <?= $class['start_time'] ?></p>
                                <p><strong>End Time:</strong> <?= $class['end_time'] ?></p>
                                <p>
                                    <strong>Attendance:</strong>
                                     <span class="attendance-<?= $class['attendance_percentage'] < 75 ? 'low' : ($class['attendance_percentage'] < 85 ? 'medium' : 'high') ?>">
                                        <?= $class['attendance_percentage'] ?>%
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No classes found for this semester.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
