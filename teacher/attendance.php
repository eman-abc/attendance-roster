<?php
// Start session to store user data
session_start();

// Include database configuration file
require_once('C:\xampp\htdocs\attendance-roster\config.php'); // Adjust the path as per your file structure

// Initialize variables
$class_name = "";
$attendance_data = []; // To hold the fetched attendance data
$attendance_err = "";

// Check if the class ID is provided
if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
} else {
    echo "Class ID is missing.";
    exit();
}

// Fetch the class name based on the class ID
$sql_class_name = "SELECT name FROM classes WHERE id = ?";
if ($stmt = $conn->prepare($sql_class_name)) {
    $stmt->bind_param("i", $class_id);

    if ($stmt->execute()) {
        $stmt->bind_result($class_name);
        $stmt->fetch();
    }
    $stmt->close();
} else {
    echo "Error fetching class name.";
    exit();
}

// Prepare the SQL query to fetch attendance data for the class grouped by date
$sql = "SELECT a.date, a.student_id, a.status, a.comment
        FROM attendance a
        JOIN student_class sc ON a.student_id = sc.student_id AND a.class_id = sc.class_id
        WHERE sc.class_id = ?
        ORDER BY a.date DESC";

// Prepare and execute the query
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $class_id);

    if ($stmt->execute()) {
        $stmt->bind_result($date, $student_id, $status, $comment);

        // Group attendance data by date
        while ($stmt->fetch()) {
            $attendance_data[$date][] = [
                'student_id' => $student_id,
                'status' => $status,
                'comment' => $comment
            ];
        }
    } else {
        $attendance_err = "Oops! Something went wrong. Please try again later.";
    }

    $stmt->close();
} else {
    $attendance_err = "Error preparing the query.";
}

// Close the connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance for Class</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/attendance.css?v=1.0">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Attendance for Class: <?php echo htmlspecialchars($class_name); ?></h2>

        <?php if (empty($attendance_data)) : ?>
            <div class="alert no-data">No attendance records found.</div>
        <?php else : ?>
            <div class="row card-container">
                <?php foreach ($attendance_data as $date => $records) : ?>
                    <div class="col-lg-4 col-md-6"> <!-- Adjusts to 3 cards per row on large screens, 2 on medium -->
                        <div class="card">
                            <div class="card-header">
                                Date: <?php echo htmlspecialchars($date); ?>
                            </div>
                            <div class="card-body">
                                <p>Number of Records: <?php echo count($records); ?></p>
                                <a href="modify_attendance.php?class_id=<?php echo $class_id; ?>&date=<?php echo urlencode($date); ?>" class="btn btn-primary">
                                    Mark Attendance
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>