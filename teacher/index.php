<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Semesters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <div class="container">
        <?php
        // Start session to access the logged-in teacher's ID
        session_start();

        // Include database configuration file
        require_once('C:\xampp\htdocs\attendance-roster\config.php');

        // Check if the user is logged in as a teacher
        if (isset($_SESSION["loggedin"]) && $_SESSION["role"] == "teacher") {
            // Get the teacher's ID from session
            $teacher_id = $_SESSION["id"];

            // SQL query to fetch semesters for the logged-in teacher
            $sql = "SELECT DISTINCT s.id, s.name, s.start_date, s.end_date, s.status
                    FROM semesters s
                    JOIN classes c ON s.id = c.semester_id
                    WHERE c.teacher_id = ?
                    ORDER BY s.start_date DESC";

            if ($stmt = $conn->prepare($sql)) {
                // Bind the teacher's ID to the prepared statement
                $stmt->bind_param("i", $teacher_id);

                // Execute the query
                if ($stmt->execute()) {
                    // Store result
                    $stmt->store_result();

                    // Check if any semesters are found
                    if ($stmt->num_rows > 0) {
                        // Bind result variables
                        $stmt->bind_result($semester_id, $semester_name, $start_date, $end_date, $status);

                        echo "<h2 class='mb-4'>Your Semesters</h2>";
                        echo "<div class='row g-4'>";  // Start row for Bootstrap cards

                        // Fetch and display the semesters
                        while ($stmt->fetch()) {
                            // Create a Bootstrap card for each semester
                            echo "<div class='col-lg-4 col-md-6 d-flex align-items-stretch'>";
                            echo "<div class='card border-primary'>";
                            echo "<div class='card-header bg-primary text-white'>";
                            echo "<h5 class='card-title mb-0'>" . htmlspecialchars($semester_name) . "</h5>";
                            echo "</div>";
                            echo "<div class='card-body d-flex flex-column'>";
                            echo "<p><strong>Start Date:</strong> " . htmlspecialchars($start_date) . "</p>";
                            echo "<p><strong>End Date:</strong> " . htmlspecialchars($end_date) . "</p>";
                            echo "<p><strong>Status:</strong> " . ucfirst(htmlspecialchars($status)) . "</p>";
                            echo "<a href='classes.php?semester_id=" . $semester_id . "' class='btn btn-primary mt-auto'>View Classes</a>";
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";  // Close card column
                        }

                        echo "</div>";  // Close row
                    } else {
                        echo "<div class='no-data alert alert-warning'>No semesters found for this teacher.</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Error: Could not execute the query. Please try again.</div>";
                }

                // Close the prepared statement
                $stmt->close();
            } else {
                echo "<div class='alert alert-danger'>Error: Could not prepare the SQL statement. Please try again.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>You are not authorized to view this page.</div>";
        }

        // Close database connection
        $conn->close();
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>