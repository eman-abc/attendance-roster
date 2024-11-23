<?php
// Start session to store user data
session_start();

// Include database configuration file
require_once('C:\xampp\htdocs\attendance-roster\config.php'); // Adjust the path as per your file structure

// Initialize variables
if (isset($_GET['class_id'], $_GET['date'])) {
    $class_id = $_GET['class_id'];
    $date = $_GET['date'];
} else {
    echo "Missing required parameters.";
    exit();
}

// Initialize error and success messages
$update_err = "";
$update_success = "";

// Prepare the SQL query to fetch attendance data for the selected class and date
$sql = "SELECT a.student_id, a.status, a.comment
        FROM attendance a
        JOIN student_class sc ON a.student_id = sc.student_id AND a.class_id = sc.class_id
        WHERE a.class_id = ? AND a.date = ?
        ORDER BY a.student_id";

$attendance_data = [];

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("is", $class_id, $date);

    if ($stmt->execute()) {
        $stmt->bind_result($student_id, $status, $comment);
        while ($stmt->fetch()) {
            $attendance_data[] = [
                'student_id' => $student_id,
                'status' => $status,
                'comment' => $comment
            ];
        }
    } else {
        $update_err = "Failed to fetch attendance data.";
    }

    $stmt->close();
} else {
    $update_err = "Error preparing the query.";
}

// Update attendance if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $new_status = $_POST['status'];
    $new_comment = $_POST['comment'];

    $update_sql = "UPDATE attendance
                   SET status = ?, comment = ?
                   WHERE class_id = ? AND date = ? AND student_id = ?";

    if ($stmt = $conn->prepare($update_sql)) {
        $stmt->bind_param("ssisi", $new_status, $new_comment, $class_id, $date, $student_id);

        if ($stmt->execute()) {
            $update_success = "Attendance updated successfully.";
        } else {
            $update_err = "Failed to update attendance.";
        }

        $stmt->close();
    } else {
        $update_err = "Error preparing the update query.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Attendance</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Modify Attendance for Class ID: <?php echo htmlspecialchars($class_id); ?> on <?php echo htmlspecialchars($date); ?></h2>

        <?php
        // Show any error messages
        if (!empty($update_err)) {
            echo "<div class='alert alert-danger'>" . htmlspecialchars($update_err) . "</div>";
        }

        // Show success message
        if (!empty($update_success)) {
            echo "<div class='alert alert-success'>" . htmlspecialchars($update_success) . "</div>";
        }
        ?>

        <form method="POST">
            <?php
            // Display form for each student to modify attendance
            foreach ($attendance_data as $attendance) {
                echo "<div class='card mb-3'>";
                echo "<div class='card-header'><strong>Student ID: " . htmlspecialchars($attendance['student_id']) . "</strong></div>";
                echo "<div class='card-body'>";

                echo "<input type='hidden' name='student_id' value='" . $attendance['student_id'] . "'>";

                echo "<div class='form-group'>";
                echo "<label for='status'>Attendance Status</label>";
                echo "<select class='form-control' name='status' required>";
                echo "<option value='present' " . ($attendance['status'] == 'present' ? 'selected' : '') . ">Present</option>";
                echo "<option value='absent' " . ($attendance['status'] == 'absent' ? 'selected' : '') . ">Absent</option>";
                echo "</select>";
                echo "</div>";

                echo "<div class='form-group'>";
                echo "<label for='comment'>Comment</label>";
                echo "<textarea class='form-control' name='comment' required>" . htmlspecialchars($attendance['comment']) . "</textarea>";
                echo "</div>";

                echo "<button type='submit' class='btn btn-primary'>Update</button>";
                echo "</div>"; // card-body
                echo "</div>"; // card
            }
            ?>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>