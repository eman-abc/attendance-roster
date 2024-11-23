<?php
// Start session to store user data
session_start();

// Include database configuration file
require_once('C:\xampp\htdocs\attendance-roster\config.php'); // Adjust the path as per your file structure

// Initialize variables
if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
} else {
    echo "Class ID is missing.";
    exit();
}

// Initialize error variables
$attendance_data = []; // To hold the fetched attendance data
$attendance_err = "";

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
</head>

<body>
    <div class="container mt-5">
        <h2>Attendance for Class ID: <?php echo htmlspecialchars($class_id); ?></h2>

        <?php
        // Show any error messages
        if (!empty($attendance_err)) {
            echo "<div class='alert alert-danger'>" . htmlspecialchars($attendance_err) . "</div>";
        }

        // Check if attendance data is available
        if (empty($attendance_data)) {
            echo "<div class='alert alert-warning'>No attendance records found.</div>";
        } else {
            // Loop through each date and display attendance in separate cards
            foreach ($attendance_data as $date => $records) {
                echo "<div class='card mb-3'>";
                echo "<div class='card-header'><strong>Date: </strong>" . htmlspecialchars($date) . "</div>";
                echo "<div class='card-body'>";

                // Link to the form page for modifying attendance on that date
                echo "<a href='modify_attendance.php?class_id=" . $class_id . "&date=" . urlencode($date) . "' class='btn btn-primary'>Modify Attendance</a>";
                echo "</div>"; // card-body
                echo "</div>"; // card
            }
        }
        ?>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>