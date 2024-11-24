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

// Fetch attendance data along with student names in a single query
$sql = "SELECT a.student_id, s.fullname AS student_name, a.status, a.comment
        FROM attendance a
        JOIN student_class sc ON a.student_id = sc.student_id AND a.class_id = sc.class_id
        JOIN user s ON a.student_id = s.id
        WHERE a.class_id = ? AND a.date = ?
        ORDER BY a.student_id";

$attendance_data = [];

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("is", $class_id, $date);

    if ($stmt->execute()) {
        $stmt->bind_result($student_id, $student_name, $status, $comment);
        while ($stmt->fetch()) {
            $attendance_data[] = [
                'student_id' => $student_id,
                'student_name' => $student_name,
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
    if (isset($_POST['status'], $_POST['comment'], $_POST['update'])) {
        $student_id = $_POST['update'];
        $new_status = $_POST['status'][$student_id];
        $new_comment = $_POST['comment'][$student_id];

        $update_sql = "UPDATE attendance
                       SET status = ?, comment = ?
                       WHERE class_id = ? AND date = ? AND student_id = ?";

        if ($stmt = $conn->prepare($update_sql)) {
            $stmt->bind_param("ssisi", $new_status, $new_comment, $class_id, $date, $student_id);

            if ($stmt->execute()) {
                $update_success = "Attendance updated successfully.";
                // Redirect to the same page with the class_id and date as query parameters
                header("Location: modify_attendance.php?class_id=" . urlencode($class_id) . "&date=" . urlencode($date));
                exit();
            } else {
                $update_err = "Failed to update attendance.";
            }

            $stmt->close();
        } else {
            $update_err = "Error preparing the update query.";
        }
    } else {
        $update_err = "Invalid form submission.";
    }
}

$conn->close(); // Close the connection after all queries
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Attendance</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/mark_attendance.css?v=1.0">
    <style>
        :root {
            /* Color Palette */
            --primary-dark: #33334d;
            --primary-mauve: #6d5c6a;
            --accent-pink: #ceb0b0;
            --background-light: #F0F1FA;

            /* Text Colors */
            --text-light: #f0f0f0;
            --text-dark: #1a1a1a;
        }

        body {
            background-color: var(--background-light);
            color: var(--text-dark);
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            padding-top: 50px;
        }

        h2 {
            color: var(--primary-mauve);
        }

        .alert {
            border-radius: 10px;
        }

        .table {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table th,
        .table td {
            text-align: center;
            padding: 15px;
        }

        .table th {
            background-color: var(--primary-dark) !important;
            color: var(--text-light) !important;
        }

        .table td select,
        .table td textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        .btn {
            background-color: var(--primary-mauve);
            color: var(--text-light);
            border-radius: 20px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: var(--accent-pink);
            color: var(--text-dark);
        }

        .form-control {
            background-color: #f9f9f9;
            border-radius: 4px;
            box-shadow: none;
            border: 1px solid #ddd;
        }

        .alert-success {
            background-color: var(--primary-mauve);
            color: var(--text-light);
        }

        .alert-danger {
            background-color: #f44336;
            color: var(--text-light);
        }

        .alert-warning {
            background-color: var(--accent-pink);
            color: var(--text-dark);
        }

        /* Responsive Design */
        @media (max-width: 768px) {

            .table th,
            .table td {
                font-size: 14px;
                padding: 10px;
            }

            .container {
                padding-top: 30px;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Modify Attendance on <?php echo htmlspecialchars($date); ?></h2>

        <?php
        // Show error message
        if (!empty($update_err)) {
            echo "<div class='alert alert-danger'>" . htmlspecialchars($update_err) . "</div>";
        }

        // Show success message
        if (!empty($update_success)) {
            echo "<div class='alert alert-success'>" . htmlspecialchars($update_success) . "</div>";
        }
        ?>

        <?php if (empty($attendance_data)) : ?>
            <div class="alert alert-warning">No attendance records found for the specified date and class.</div>
        <?php else : ?>
            <form method="POST">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Status</th>
                            <th>Comment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendance_data as $record) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($record['student_name']); ?></td>
                                <td>
                                    <select class="form-control" name="status[<?php echo htmlspecialchars($record['student_id']); ?>]" required>
                                        <option value="present" <?php echo ($record['status'] === 'present') ? 'selected' : ''; ?>>Present</option>
                                        <option value="absent" <?php echo ($record['status'] === 'absent') ? 'selected' : ''; ?>>Absent</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea class="form-control" name="comment[<?php echo htmlspecialchars($record['student_id']); ?>]"><?php echo htmlspecialchars($record['comment']); ?></textarea>
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-primary" name="update" value="<?php echo htmlspecialchars($record['student_id']); ?>">Update</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>