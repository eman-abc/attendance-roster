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
        <?php
        // Include necessary files (e.g., database connection)
        require_once('C:\xampp\htdocs\attendance-roster\config.php');

        // Start the session (if not already started)
        session_start();

        // Check if a semester ID is passed in the URL
        if (!isset($_GET['semester_id'])) {
            echo "<div class='alert alert-danger'>Semester ID not provided.</div>";
            exit();
        }

        $semester_id = $_GET['semester_id'];

        // Get the teacher ID from the session
        $teacher_id = $_SESSION['id']; // Assume the teacher's ID is stored in the session

        // Query to fetch all classes for this teacher in the specified semester
        $sql = "SELECT c.id, c.name, c.start_time, c.end_time
                FROM classes c
                JOIN semesters s ON c.semester_id = s.id
                WHERE c.semester_id = ? AND s.status = 'current' AND c.teacher_id = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ii", $semester_id, $teacher_id);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if there are any classes
            if ($result->num_rows > 0) {
                echo '<h2 class="mb-4">Classes for Semester</h2>';
                echo '<div class="row g-4">'; // Bootstrap grid system with gap

                // Output each class in a card format
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col-lg-4 col-md-6 d-flex align-items-stretch">';
                    echo '<div class="card border-primary">';
                    echo '<div class="card-body d-flex flex-column">';
                    echo '<h5 class="card-title text-primary">' . htmlspecialchars($row['name']) . '</h5>';
                    echo '<p class="card-text"><strong>Start Time:</strong> ' . htmlspecialchars($row['start_time']) . '</p>';
                    echo '<p class="card-text"><strong>End Time:</strong> ' . htmlspecialchars($row['end_time']) . '</p>';
                    echo '<a href="attendance.php?class_id=' . htmlspecialchars($row['id']) . '" class="btn btn-primary mt-auto">Mark Attendance</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>'; // Close card column
                }

                echo '</div>'; // Close row
            } else {
                echo '<div class="alert alert-warning text-center">No classes found for this semester.</div>';
            }

            $stmt->close();
        } else {
            echo "<div class='alert alert-danger'>Failed to fetch classes. Please try again later.</div>";
        }

        // Close the database connection
        $conn->close();
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>