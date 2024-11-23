<?php
// Start session to store user data after login
session_start();

// Include database configuration file
require_once('C:\xampp\htdocs\attendance-roster\config.php');

// Define variables and initialize them as empty
$email = $password = "";
$email_err = $password_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if email is empty
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($email_err) && empty($password_err)) {

        // Prepare SQL statement to check if email exists
        $sql = "SELECT id, email, role, password FROM user WHERE email = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to prepared statement
            $stmt->bind_param("s", $param_email);

            // Set parameters
            $param_email = $email;

            // Execute statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                // Check if email exists
                if ($stmt->num_rows == 1) {
                    // Bind the result to variables (id, email, password)
                    $stmt->bind_result($id, $email_from_db, $role, $password_from_db);

                    // Fetch the data for the user
                    if ($stmt->fetch()) {
                        // Debug: Show fetched user data
                        echo "User ID: " . $id . "<br>";
                        echo "User Email: " . $email_from_db . "<br>";
                        echo "User Password: " . $password_from_db . "<br>";
                        echo "Role: " . $role . "<br>";

                        // Compare the entered password with the stored password
                        if ($password === $password_from_db) {
                            // Password is correct, start a new session
                            session_start();

                            // Store session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email_from_db;
                            $_SESSION["role"] = $role;

                            // Redirect based on role
                            if ($role == 'student') {
                                header("location: ../student/index.php");
                            } elseif ($role == 'teacher') {
                                header("location: ../teacher/index.php");
                            } elseif ($role == 'admin') {
                                header("location: ../admin/index.php");
                            }
                            exit();
                        } else {
                            $password_err = "The password you entered was not correct.";
                        }
                    }
                } else {
                    $email_err = "No account found with that email.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="master.css">
</head>

<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h2>Login</h2>
                <p class="text-muted">Please fill in your credentials to login.</p>
            </div>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <!-- Email Field -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                    <div class="invalid-feedback">
                        <?php echo $email_err; ?>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                    <div class="invalid-feedback">
                        <?php echo $password_err; ?>
                    </div>
                </div>

                <!-- Login Button -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>

            <p class="form-text mt-3">Don't have an account? <a href="register.php">Register here</a>.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>