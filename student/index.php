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

// Fetch semesters and attendance data for the logged-in student
$student_id = $_SESSION['id'];
$sql = "SELECT s.id AS semester_id, s.name, s.start_date, s.end_date, 
               CASE 
                   WHEN CURRENT_DATE BETWEEN s.start_date AND s.end_date THEN 'Current'
                   ELSE 'Completed'
               END AS status
        FROM semesters s
        INNER JOIN classes c ON s.id = c.semester_id
        INNER JOIN attendance a ON c.id = a.class_id
        WHERE a.student_id = ?
        GROUP BY s.id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$semesters = [];
while ($row = $result->fetch_assoc()) {
    $semesters[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <div class="container">
        <h1 class="my-4 text-center ">Your Semesters</h1>
        <div class="row">
            <?php if (count($semesters) > 0): ?>
                <?php foreach ($semesters as $semester): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($semester['name']) ?></h5>
                                <p><strong>Start Date:</strong> <?= $semester['start_date'] ?></p>
                                <p><strong>End Date:</strong> <?= $semester['end_date'] ?></p>
                                <p><strong>Status:</strong> <?= $semester['status'] ?></p>
                                <a href="classes.php?semester_id=<?= $semester['semester_id'] ?>" class="btn btn-primary w-100">View Classes</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No semesters found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
